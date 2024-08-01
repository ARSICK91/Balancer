<?php

namespace App\Repository;

use App\Entity\Process;

interface ProcessRepositoryInterface
{
    public function find_process(int $id): Process;
    public function find_processes(): array;
    public function addProcess(Process $process): bool;
    public function delProcess(Process $process): bool;
    public function findAllExcept(Process $process): array;
}
