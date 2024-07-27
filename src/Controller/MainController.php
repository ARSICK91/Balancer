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
        $machines= $machineRepository->find_machines();
        $processes = $processRepository->find_processes();
        
        return $this->render('main.html.twig',['machines'=>$machines,'processes'=>$processes]);
    }
}