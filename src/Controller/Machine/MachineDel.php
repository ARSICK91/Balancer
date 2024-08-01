<?php
namespace App\Controller\Machine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Machine;
use App\Repository\MachineRepository;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ProcessRepository;
use App\Service\AllRebalance;
class MachineDel extends AbstractController{

    #[Route('/machine/delete/{id}', name: "delete_machine")]
    public function delete(Request $request, Machine $machine, MachineRepository $machineRepository,ProcessRepository $processRepository, AllRebalance $allRebalance): Response
    {
        if ($request->isMethod('POST')) 
        {
        $entityManager = $machineRepository->getEntityManager();
        $entityManager->beginTransaction();

        try {
            $machines = $machineRepository->findAllExcept($machine);
            $processes = $processRepository->find_processes();

            if ($allRebalance->balanceAllProcesses($processes, $machines)) 
            {
                $machineRepository->remove($machine);
                $entityManager->flush(); 
                $entityManager->commit(); 
                $this->addFlash('success', 'Машина успешно удалена');
                return $this->redirectToRoute('main-table');
            } 
            else 
            {
                $entityManager->rollback();
                $this->addFlash('error', 'Не удалось удалить машину');
            }
        } catch (\Exception $e) {
            $entityManager->rollback();
            $this->addFlash('error', 'Произошла ошибка при удалении машины');
        }
    }

    return $this->render('Machine/delete.html.twig', ['machine' => $machine]);

    }
    
}
