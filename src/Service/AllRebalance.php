<?php

namespace App\Service;

use App\Entity\Process;
use App\Entity\Machine;
use Doctrine\ORM\EntityManagerInterface;

class AllRebalance{
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }
    public function balanceAllProcesses(array $processes, array $machines): bool
{
   $this->entityManager->beginTransaction();

   try {
       if (empty($processes)) {
           return true;
       }

       foreach ($processes as $process) {
           $process->delMyMachine();
           $this->entityManager->flush();
       }

       $processesToMove = [];
       $processes = $this->sortBalanceProcess($processes);

       foreach ($processes as $process) {
           $machinesToMove = [];

           foreach ($machines as $targetMachine) {
               if ($this->canAddProcess($targetMachine, $process)) {
                   $machinesToMove[] = $targetMachine;
               }
           }

           if (empty($machinesToMove)) {
               $this->rollbackProcesses($processesToMove);
               $this->entityManager->rollback();
               return false;
           }

           $machinesToMove = $this->sortBalanceMachine($machinesToMove);
           $targetMachine = ($process->balance() >= 0) ? $machinesToMove[0] : end($machinesToMove);
           $targetMachine->addMyProcess($process);
           $processesToMove[] = [
               'process' => $process,
               'machine' => $targetMachine
           ];
       }
       
       $this->entityManager->flush();
       $this->entityManager->commit();
       return true;

   } catch (\Exception $e) {
       $this->entityManager->rollback();
       $this->entityManager->close();
       error_log($e->getMessage());
       throw $e;
   }
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
            $this->entityManager->flush(); 
        }
    }

    private function canAddProcess(Machine $machine, Process $process): bool
    {
        [$availableMemory, $availableCpu] = $this->calculateLoad($machine, $process);
        return $availableMemory >= 0 && $availableCpu >= 0;
    }

    private function calculateLoad(Machine $machine, Process $process): array
    {
        $currentMemoryUsage = array_sum(array_map(fn($p) => $p->getNeedMemory(), $machine->getMyProcesses()->toArray()));
        $currentCpuUsage = array_sum(array_map(fn($p) => $p->getNeedCore(), $machine->getMyProcesses()->toArray()));

        $processMemoryUsage = $process->getNeedMemory();
        $processCpuUsage = $process->getNeedCore();

        $totalMemory= $machine->getTotalMemory();
        $totalCore = $machine->getTotalCore();
        return [
            $totalMemory - $currentMemoryUsage - $processMemoryUsage,
            $totalCore - $currentCpuUsage - $processCpuUsage
        ];
    }
}