<?php

namespace App\Service;

use App\Entity\Process;
use App\Entity\Machine;
use Doctrine\ORM\EntityManagerInterface;

class AllRebalance {
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    // Метод для балансировки всех процессов
    public function balanceAllProcesses(array $processes, array $machines): bool {
        // Начинаем транзакцию
        $this->entityManager->beginTransaction();

        try {
            // Если массив процессов пуст, возвращаем true
            if (empty($processes)) {
                return true;
            }

            // Удаляем машины у всех процессов
            foreach ($processes as $process) {
                $process->delMyMachine();
                $this->entityManager->flush(); 
            }

            $processesToMove = [];
            // Сортируем процессы для балансировки
            $processes = $this->sortBalanceProcess($processes);

            // Перебираем каждый процесс
            foreach ($processes as $process) {
                $machinesToMove = [];

                // Ищем машины, которые могут принять процесс
                foreach ($machines as $targetMachine) {
                    if ($this->canAddProcess($targetMachine, $process)) {
                        $machinesToMove[] = $targetMachine;
                    }
                }

                // Если нет доступных машин, откатываем изменения и возвращаем false
                if (empty($machinesToMove)) {
                    $this->rollbackProcesses($processesToMove);
                    $this->entityManager->rollback();
                    return false;
                }

                // Сортируем доступные машины
                $machinesToMove = $this->sortBalanceMachine($machinesToMove);
                // Выбираем целевую машину в зависимости от баланса процесса
                $targetMachine = ($process->balance() >= 0) ? $machinesToMove[0] : end($machinesToMove);
                $targetMachine->addMyProcess($process); // Добавляем процесс к выбранной машине
                $processesToMove[] = [
                    'process' => $process,
                    'machine' => $targetMachine
                ];
            }

            // Применяем все изменения к базе данных
            $this->entityManager->flush();
            $this->entityManager->commit(); // Завершаем транзакцию
            return true;

        } catch (\Exception $e) {
            // В случае ошибки откатываем транзакцию и закрываем соединение
            $this->entityManager->rollback();
            $this->entityManager->close();
            error_log($e->getMessage()); // Логируем сообщение об ошибке
            throw $e; // Бросаем исключение дальше
        }
    }

    // Метод для сортировки процессов по балансу
    private function sortBalanceProcess(array $processes): array {
        usort(
            $processes,
            function (Process $a, Process $b): int {
                return $a->balance() <=> $b->balance(); // Сравниваем баланс процессов
            }
        );

        return $processes;
    }
    
    // Метод для сортировки машин по балансу
    private function sortBalanceMachine(array $machines): array {
        usort(
            $machines,
            function (Machine $a, Machine $b): int {
                return $a->balancer() <=> $b->balancer(); // Сравниваем баланс машин
            }
        );

        return $machines;
    }

    // Метод для отката процессов, если не удалось их переместить
    private function rollbackProcesses(array $processesToMove): void {
        foreach ($processesToMove as $entry) {
            $process = $entry['process'];
            $machine = $entry['machine'];
            $machine->removeMyProcess($process); // Удаляем процесс из машины
            $this->entityManager->flush(); // Применяем изменения к базе данных
        }
    }

    // Метод для проверки, может ли машина принять процесс
    private function canAddProcess(Machine $machine, Process $process): bool {
        [$availableMemory, $availableCpu] = $this->calculateLoad($machine, $process);
        return $availableMemory >= 0 && $availableCpu >= 0; // Проверяем, достаточно ли ресурсов
    }

    // Метод для расчета загрузки машины с учетом процесса
    private function calculateLoad(Machine $machine, Process $process): array {
        // Считаем текущее использование памяти и CPU
        $currentMemoryUsage = array_sum(array_map(fn($p) => $p->getNeedMemory(), $machine->getMyProcesses()->toArray()));
        $currentCpuUsage = array_sum(array_map(fn($p) => $p->getNeedCore(), $machine->getMyProcesses()->toArray()));

        $processMemoryUsage = $process->getNeedMemory(); // Память, необходимая процессу
        $processCpuUsage = $process->getNeedCore(); // CPU, необходимый процессу

        $totalMemory = $machine->getTotalMemory(); // Общая память машины
        $totalCore = $machine->getTotalCore(); // Общий CPU машины

        // Возвращаем доступную память и CPU
        return [
            $totalMemory - $currentMemoryUsage - $processMemoryUsage,
            $totalCore - $currentCpuUsage - $processCpuUsage
        ];
    }
}
