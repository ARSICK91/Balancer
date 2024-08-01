<?php

namespace App\Controller\Machine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\MachineType;
use App\Entity\Machine;
use App\Repository\MachineRepository;
use App\Service\AllRebalance;
use App\Repository\ProcessRepository;
class MachineAdd extends AbstractController
{
    #[Route('/machine/add', name: "add_machine")]
    public function add(Request $request,MachineRepository $machineRepository,AllRebalance $allRebalance, ProcessRepository $processRepository)
    {
        $machine = new Machine();
        $form = $this->createForm(MachineType::class, $machine,[
            'attr' => [
                'class' => 'column-flex', 
            ],
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $machineRepository->add($machine);
            $machines = $machineRepository->find_machines();
            $processes = $processRepository->find_processes();
            $allRebalance->balanceAllProcesses($processes,$machines);
            $this->addFlash("success","Машина успешно добавлена");
            return $this->redirectToRoute('main-table');
        }
        
        return $this->render("Machine/add.html.twig", ['form'=>$form->createView()]);
    }
}