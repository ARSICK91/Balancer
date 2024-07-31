<?php

namespace App\Service;

use App\Entity\Machine;
use App\Repository\MachineRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\AllRebalance;

class MachineRebalance
{
    private MachineRepository $machineRepository;
    private EntityManagerInterface $entityManager;
    private AllRebalance $allRebalance;

    public function __construct(
        MachineRepository $machineRepository,
        EntityManagerInterface $entityManager,
        AllRebalance $allRebalance
    ) {
        $this->machineRepository = $machineRepository;
        $this->entityManager = $entityManager;
        $this->allRebalance = $allRebalance;
    }
    public function addMachine(Machine $machine): bool
    {
        $this->machineRepository->add($machine);

        if ($this->allRebalance->balanceAllProcesses()){
            return true;
        }

        $this->machineRepository->remove($machine);
        return false;
    }
    
    public function delMachine(Machine $machine): bool
    {
        $this->machineRepository->remove($machine);

        if ($this->allRebalance->balanceAllProcesses())
        {
            return true;
        }
        $this->machineRepository->add($machine);
        return false;
    }
}
    
