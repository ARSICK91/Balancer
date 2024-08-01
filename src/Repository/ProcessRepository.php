<?php

namespace App\Repository;

use App\Entity\Process;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\MachineRepository;
use App\Service\AllRebalance;

/**
 * @extends ServiceEntityRepository<Process>
 */
class ProcessRepository extends ServiceEntityRepository implements ProcessRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, AllRebalance $allRebalance)
    {
        parent::__construct($registry, Process::class);
        $this->entityManager = $registry->getManager();
        $this->allRebalance = $allRebalance;
    }

    public function find_process(int $id): Process
    {
        return $this->findOneBy(["id"=>$id]);
    }

    public function find_processes(): array
    {
        return $this->findAll();
    }
    public function addProcess(Process $process): bool
    {
        try {
            $this->entityManager->persist($process);
            $this->entityManager->flush();
            return true; 
        } catch (\Exception $e) {
            return false; 
        }
    }

    public function delProcess(Process $process): bool
    {
        try {
            $this->entityManager->remove($process);
            $this->entityManager->flush();
            return true; 
        } catch (\Exception $e) {
            return false; 
        }
    }
    public function findAllExcept(Process $process): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.id != :processId')
            ->setParameter('processId', $process->getId())
            ->getQuery()
            ->getResult();
    }
}
