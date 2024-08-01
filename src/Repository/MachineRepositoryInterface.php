<?php
namespace App\Repository;

use App\Entity\Machine;

interface MachineRepositoryInterface
{
    public function find_machine(int $id): ?Machine;
    public function find_machines(): array;
    public function add(Machine $machine, bool $flush = true): bool;
    public function remove(Machine $machine, bool $flush = true): bool;
    public function findAllExcept(Machine $excludedMachine): array;
}
