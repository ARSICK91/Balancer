<?php

namespace App\Repository;

use App\Entity\Machine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @extends ServiceEntityRepository<Machine>
 */
class MachineRepository extends ServiceEntityRepository implements MachineRepositoryInterface
{
    private $allRebalance;
    private $entityManager;
    private $processRepository;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Machine::class);
        $this->entityManager = $registry->getManager();
    }
    public function add(Machine $machine, bool $flush = true): bool
    {
        try {
            $this->entityManager->persist($machine);
            if ($flush) {
                $this->entityManager->flush();
            }
            return true; 
        } catch (\Exception $e) {
            return false; 
        }
    }
    public function remove(Machine $machine, bool $flush = true): bool
    {
        try {
            $this->entityManager->remove($machine);

            if ($flush) {$this->entityManager->flush();}

            return true; 
        } catch (\Exception $e) {
            return false; 
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
    public function findAllExcept(Machine $machine): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.id != :machineId')
            ->setParameter('machineId', $machine->getId())
            ->getQuery()
            ->getResult();
    }
}
