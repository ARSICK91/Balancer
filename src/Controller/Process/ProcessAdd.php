<?php

namespace App\Controller\Process;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ProcessType;
use App\Entity\Process;
use App\Repository\ProcessRepository;
use App\Repository\MachineRepository;
use App\Service\AllRebalance;
use Symfony\Component\HttpFoundation\Response;
class ProcessAdd extends AbstractController
{
#[Route('/process/add', name: "add_process")]
public function add(Request $request, ProcessRepository $processRepository, MachineRepository $machineRepository, AllRebalance $allRebalance): Response
{
    $process = new Process();
    $form = $this->createForm(ProcessType::class, $process);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) 
    {
        $entityManager = $processRepository->getEntityManager();
        $entityManager->beginTransaction();

        try {
            $processRepository->addProcess($process);
            $machines = $machineRepository->find_machines();
            $processes = $processRepository->find_processes();

            if ($allRebalance->balanceAllProcesses($processes, $machines)) {
                $entityManager->commit(); 
                $this->addFlash("success","Процесс успешно добавлен");
                return $this->redirectToRoute('main-table');
            }
            $entityManager->rollback();
            $this->addFlash("error", "Не удалось добавить процесс");
            $processRepository->delProcess($process); 
        } catch (\Exception $e) {

            $entityManager->rollback();
            $this->addFlash("error", "Произошла ошибка при добавлении процесса");
        }
    }

    return $this->render("Process/add.html.twig", ['form' => $form->createView()]);
}

}

