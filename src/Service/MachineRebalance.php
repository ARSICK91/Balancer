<?php

namespace App\Service;

use App\Entity\Process;
use App\Entity\Machine;
use App\Repository\ProcessRepository;
use App\Repository\MachineRepository;
use Doctrine\ORM\EntityManagerInterface;


class MachineRebalance
{
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

    public function canRemoveMachine(Machine $machine): bool
{
    $processes = $machine->getMyProcesses()->toArray();

    if (empty($processes)) {
        return true;
    }

    $allMachines = $this->machineRepository->findAll();
    $processes = $this->sortBalanceProcess($processes);

    $processesToMove = [];

    foreach ($processes as $process) {
        $machinesToMove = [];
    
        foreach ($allMachines as $targetMachine) {
            if ($this->canAddProcess($targetMachine, $process)) {
                $machinesToMove[] = $targetMachine;
            }
        }
    
        if (empty($machinesToMove)) {
            $this->rollbackProcesses($processesToMove);
            return false; // Не удалось перенести хотя бы один процесс
        }
    
        $machinesToMove = $this->sortBalanceMachine($machinesToMove);
        $targetMachine = ($process->balancer() >= 0) ? $machinesToMove[0] : end($machinesToMove);
        $targetMachine->addMyProcess($process);
        $processesToMove[] = [
            'process' => $process,
            'machine' => $targetMachine
        ];
    }
    
    foreach ($processes as $process) {
        $machine->removeMyProcess($process);
    }
    
    $this->entityManager->flush(); // Сохраняем изменения в базе данных
    
    return true; // Все процессы успешно перенесены
}
    
    private function sortBalanceProcess(array $processes): array
    {
        usort(
            $processes,
            function (Process $a, Process $b): int {
                return $a->balance() <=> $b->balance();
            }
        );
    
        return $processes;
    }
    
    private function sortBalanceMachine(array $machines): array
    {
        usort(
            $machines,
            function (Machine $a, Machine $b): int {
                return $a->balancer() <=> $b->balancer();
            }
        );
    
        return $machines;
    }
    
    private function rollbackProcesses(array $processesToMove): void
    {
        foreach ($processesToMove as $entry) {
            $process = $entry['process'];
            $machine = $entry['machine'];
    
            $machine->removeMyProcess($process); 
        }
    
        $this->entityManager->flush(); 
    }

    private function canAddProcess(Machine $machine, Process $process): bool
    {
        [$availableMemory, $availableCpu] = $this->calculateLoad($machine, $process);
        return $availableMemory >= 0 && $availableCpu >= 0;
    }

    private function calculateLoad(Machine $machine, Process $process): array
    {
        $currentMemoryUsage = array_sum(array_map(fn($p) => $p->getTotalMemory(), $machine->getMyProcesses()->toArray()));
        $currentCpuUsage = array_sum(array_map(fn($p) => $p->getTotalCore(), $machine->getMyProcesses()->toArray()));

        $processMemoryUsage = $process->getNeedMemory();
        $processCpuUsage = $process->getNeedCore();

        return [
            $currentMemoryUsage + $processMemoryUsage,
            $currentCpuUsage + $processCpuUsage
        ];
    }
}