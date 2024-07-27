<?php

namespace App\Controller\Machine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\MachineType;
use App\Entity\Machine;
use App\Repository\MachineRepository;
class MachineAdd extends AbstractController
{
    #[Route('/machine/add', name: "add_machine")]
    public function add(Request $request, EntityManagerInterface $entityManager,MachineRepository $machineRepository)
    {

        $machine = new Machine();
        $form = $this->createForm(MachineType::class, $machine);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $machineRepository->add($machine);
            return $this->redirectToRoute('main-table');
        }
        
        return $this->render("Machine/add.html.twig", ['form'=>$form->createView()]);
    }
}