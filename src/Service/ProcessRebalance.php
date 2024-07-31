<?php

namespace App\Service;
use App\Entity\Process;
use App\Entity\Machine;
use App\Repository\ProcessRepository;
use App\Repository\MachineRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProcessRebalance
{
    private ProcessRepository $processRepository;
    private MachineRepository $machineRepository;
    private EntityManagerInterface $em;

    public function __construct(ProcessRepository $processRepository, MachineRepository $machineRepository, EntityManagerInterface $em)
    {
        $this->processRepository = $processRepository;
        $this->machineRepository = $machineRepository;
        $this->em = $em;
    }

    public function addProcess(int $NeedCore, int $NeedMemory): void
    {
        $process = new Process();
        $process->setNeedMemory($NeedMemory);
        $process->setNeedCore($NeedCore);

        $this->em->persist($process);
        $this->em->flush();

        $this->rebalance();
    }

    public function removeProcess(int $processId): void
    {
        $process = $this->processRepository->find($processId);
        if ($process) {
            $this->em->remove($process);
            $this->em->flush();

            $this->rebalance();
        }
    }

    public function rebalance(): void
    {
        // Загрузите все процессы и машины
        $processes = $this->processRepository->findAll();
        $machines = $this->machineRepository->findAll();

        // Очистите все текущие процессы на машинах
        foreach ($machines as $machine) {
            $machine->getMyProcesses()->clear();
        }

        // Распределите процессы между машинами
        foreach ($processes as $process) {
            $bestMachine = $this->findBestMachine($process);
            if ($bestMachine) {
                $bestMachine->addMyProcess($process);
            }
        }

        $this->em->flush();
    }

    private function findBestMachine(Process $process): ?Machine
    {
        $machines = $this->machineRepository->findAll();
        $bestMachine = null;
        $minLoad = PHP_INT_MAX;

        foreach ($machines as $machine) {
            $currentLoad = $this->calculateLoad($machine, $process);
            if ($currentLoad < $minLoad) {
                $minLoad = $currentLoad;
                $bestMachine = $machine;
            }
        }

        return $bestMachine;
    }

    private function calculateLoad(Machine $machine, Process $process): int
    {
        $currentMemoryUsage = array_sum(array_map(fn($p) => $p->getNeedMemory(), $machine->getMyProcesses()->toArray()));
        $currentCpuUsage = array_sum(array_map(fn($p) => $p->getNeedCore(), $machine->getMyProcesses()->toArray()));

        $availableMemory = $machine->getTotalMemory() - $currentMemoryUsage;
        $availableCpu = $machine->getTotalCore() - $currentCpuUsage;

        if ($availableMemory < $process->getNeedMemory() || $availableCpu < $process->getNeedCore()) {
            return PHP_INT_MAX;
        }

        return ($availableMemory - $process->getNeedMemory()) + ($availableCpu - $process->getNeedCore());
    }
}