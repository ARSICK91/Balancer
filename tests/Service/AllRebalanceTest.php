<?php

namespace App\Tests\Service;

use App\Entity\Machine;
use App\Entity\Process;
use App\Service\AllRebalance;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class AllRebalanceTest extends TestCase
{
    private $entityManager;
    private $allRebalance;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->allRebalance = new AllRebalance($this->entityManager);
    }

    public function testBalanceAllProcessesSuccess()
    {
        $process = $this->createMock(Process::class);
        $process->method('balance')->willReturn(1);
        $process->method('delMyMachine');

        $machine = $this->createMock(Machine::class);
        $machine->method('balancer')->willReturn(2);
        $machine->method('addMyProcess')->with($process);
        $machine->method('getMyProcesses')->willReturn(new \Doctrine\Common\Collections\ArrayCollection());

        $this->entityManager->method('beginTransaction');
        $this->entityManager->method('flush');
        $this->entityManager->method('commit');

        $result = $this->allRebalance->balanceAllProcesses([$process], [$machine]);

        $this->assertTrue($result);
    }

    public function testBalanceAllProcessesNoAvailableMachines()
    {
        $process = $this->createMock(Process::class);
        $process->method('balance')->willReturn(1);
        $process->method('delMyMachine');

        $this->entityManager->method('beginTransaction');
        $this->entityManager->method('flush');
        $this->entityManager->method('rollback');

        $result = $this->allRebalance->balanceAllProcesses([$process], []);

        $this->assertFalse($result);
    }

    public function test2BalanceAllProcessesNoAvailableMachines()
    {
        $process = $this->createMock(Process::class);
        $process->method('getNeedMemory')->willReturn(1000);
        $process->method('getNeedCore')->willReturn(1000);
    
        $machine = $this->createMock(Machine::class);
        $machine->method('getTotalMemory')->willReturn(1); 
        $machine->method('getTotalCore')->willReturn(1); 
        $machine->method('getMyProcesses')->willReturn(new \Doctrine\Common\Collections\ArrayCollection());
        $machine->method('getName')->willReturn('TestMachine');
    
        $this->entityManager->method('beginTransaction');
        $this->entityManager->method('flush');
        $this->entityManager->method('rollback');
    
        $result = $this->allRebalance->balanceAllProcesses([$process], [$machine]);
    
        $this->assertFalse($result);
    }
    
    public function test3BalanceAllProcessesSuccess()
    {
        $process1 = $this->createMock(Process::class);
        $process1->method('balance')->willReturn(1);
        $process1->method('delMyMachine');
    
        $process2 = $this->createMock(Process::class);
        $process2->method('balance')->willReturn(1);
        $process2->method('delMyMachine');
    
        $machine1 = $this->createMock(Machine::class);
        $machine1->method('balancer')->willReturn(2);
        $machine1->method('addMyProcess')->with($process1);
        $machine1->method('getMyProcesses')->willReturn(new \Doctrine\Common\Collections\ArrayCollection());
    
        $machine2 = $this->createMock(Machine::class);
        $machine2->method('balancer')->willReturn(2);
        $machine2->method('addMyProcess')->with($process2);
        $machine2->method('getMyProcesses')->willReturn(new \Doctrine\Common\Collections\ArrayCollection());
    
        $this->entityManager->method('beginTransaction');
        $this->entityManager->method('flush');
        $this->entityManager->method('commit');
    
        $result = $this->allRebalance->balanceAllProcesses([$process1, $process2], [$machine1, $machine2]);
    
        $this->assertTrue($result);
    }
    
    public function testBalanceAllProcessesWithException()
    {
        $process = $this->createMock(Process::class);
        $process->method('balance')->willReturn(1);
        $process->method('delMyMachine');

        $machine = $this->createMock(Machine::class);
        $machine->method('balancer')->willReturn(2);
        $machine->method('getMyProcesses')->willReturn(new \Doctrine\Common\Collections\ArrayCollection());

        $this->entityManager->method('beginTransaction');
        $this->entityManager->method('flush')->will($this->throwException(new \Exception('Error')));
        $this->entityManager->method('rollback');

        $this->expectException(\Exception::class);

        $this->allRebalance->balanceAllProcesses([$process], [$machine]);
    }
}
