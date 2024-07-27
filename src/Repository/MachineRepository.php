<?php

namespace App\Repository;

use App\Entity\Machine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Machine>
 */
class MachineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Machine::class);
        $this->entityManager = $registry->getManager();
    }
    public function add(Machine $machine, bool $flush = true): void
    {
        $this->entityManager->persist($machine);

        if ($flush) {
            $this->entityManager->flush();
        }
    }
    public function remove(Machine $machine, bool $flush = true): void
    {
        $this->entityManager->remove($machine);

        if ($flush) {
            $this->entityManager->flush();
        }
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
