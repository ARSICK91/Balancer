<?php
namespace App\Controller\Process;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Process;
use App\Repository\ProcessRepository;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\MachineRepository;
use App\Service\AllRebalance;
class ProcessDel extends AbstractController{

    #[Route('/process/delete/{id}', name: "delete_process")]
    public function delete(Request $request, Process $process, MachineRepository $machineRepository,ProcessRepository $processRepository, AllRebalance $allRebalance):Response 
    {

        if ($request->isMethod('POST')) {

            $machines = $machineRepository->find_machines();
            $processes = $processRepository->findAllExcept($process);

            if($allRebalance->balanceAllProcesses($processes,$machines))
            {
                $processRepository->delProcess($process); 
                $this->addFlash('success','Процесс успешно удалён');
                return $this->redirectToRoute('main-table');
            }
            $this->addFlash('error','Не удалось удалить процесс');
        }
        return $this->render('Process/delete.html.twig', ['process' => $process]);

    }
    
}
