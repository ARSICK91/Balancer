<?php

namespace App\Service;

use App\Entity\Process;
use App\Entity\Machine;
use App\Repository\ProcessRepository;
use App\Repository\MachineRepository;
use Doctrine\ORM\EntityManagerInterface;

class AllRebalance{
    private ProcessRepository $processRepository;
    private MachineRepository $machineRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ProcessRepository $processRepository,
        MachineRepository $machineRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->processRepository = $processRepository;
        $this->machineRepository = $machineRepository;
        $this->entityManager = $entityManager;
    }
    public function AllRebalance(Process $process = null, Machine $machine = null): bool
    {
        $process ??= new Process();
        $machine ??= new Machine();
        //короче адаптировать тута  эту функцию но только вообще для всех процессов public function canRemoveMachine(Machine $machine): bool


        return true; 
    }
}