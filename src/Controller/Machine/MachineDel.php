<?php
namespace App\Controller\Machine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Machine;
use App\Repository\MachineRepository;
use Symfony\Component\HttpFoundation\Response;
class MachineDel extends AbstractController{

    #[Route('/machine/delete/{id}', name: "delete_machine")]
    public function delete(Request $request, Machine $machine, MachineRepository $machineRepository): Response
    {

        if ($request->isMethod('POST')) {
            $machineRepository->remove($machine); 
            return $this->redirectToRoute('main-table'); 
        }
    
        return $this->render('Machine/delete.html.twig', ['machine' => $machine]);
    }
    
}
