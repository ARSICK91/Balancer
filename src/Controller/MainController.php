<?php
namespace App\Controller;

use App\Service\BalancerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MachineRepository;
use App\Repository\ProcessRepository;
use App\Entity\Process;
use App\Entity\Machine;

class MainController extends AbstractController
{
    #[Route('/', name: 'main-table')]
    public function main_table(MachineRepository $machineRepository, ProcessRepository $processRepository): Response
    {
         // Логирование использования памяти
         error_log('Memory usage before: ' . memory_get_usage());
        // $machines= $machineRepository->find_machines();
        // $processes = $processRepository->find_processes();
        error_log('Memory usage after: ' . memory_get_usage());
        
        return $this->render('main.html.twig');
    }
}