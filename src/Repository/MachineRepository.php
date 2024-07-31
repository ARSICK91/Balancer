<?php

namespace App\Repository;

use App\Entity\Machine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\AllRebalance;
/**
 * @extends ServiceEntityRepository<Machine>
 */
class MachineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, AllRebalance $allRebalance)
    {
        parent::__construct($registry, Machine::class);
        $this->entityManager = $registry->getManager();
        $this->allRebalance = $allRebalance;
    }
    public function add(Machine $machine, bool $flush = true): bool
    {
        $this->entityManager->persist($machine);
        if ($flush) {$this->entityManager->flush();}

        if ($this->allRebalance->balanceAllProcesses()){return true;}

        $this->entityManager->remove($machine);
        if ($flush) {$this->entityManager->flush();}
        return false;
    }
    public function remove(Machine $machine, bool $flush = true): bool
    {
        $this->entityManager->remove($machine);
        if ($flush) {$this->entityManager->flush();}
        if ($this->allRebalance->balanceAllProcesses()){return true;}
        $this->entityManager->persist($machine);
        if ($flush) {$this->entityManager->flush();}
        return false;

    }
    public function find_machine(int $id): ?Machine
    {
        return $this->findOneBy(["id"=> $id]);
    }
    public function find_machines(): array
    {
        return $this->findAll();
    }
}
