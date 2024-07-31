<?php

namespace App\Repository;

use App\Entity\Process;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\AllRebalance;
/**
 * @extends ServiceEntityRepository<Process>
 */
class ProcessRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,AllRebalance $allRebalance)
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
        $this->entityManager->persist($process);
        $this->entityManager->flush();
        if($this->allRebalance->balanceAllProcesses()) {return true;}
        $this->entityManager->remove($process);
        $this->entityManager->flush();
        return false;
    }

    public function delProcess(Process $process): bool
    {
        $this->entityManager->remove($process);
        $this->entityManager->flush();
        if($this->allRebalance->balanceAllProcesses()) {return true;}
        $this->entityManager->persist($process);
        $this->entityManager->flush();
        return false;
    }
}
