<?php

namespace App\Entity;

use App\Repository\MachineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MachineRepository::class)]
class Machine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $total_memory = null;

    #[ORM\Column(nullable: true)]
    private ?int $total_core = null;

    /**
     * @var Collection<int, Process>
     */
    #[ORM\OneToMany(targetEntity: Process::class, mappedBy: 'my_machine')]
    private Collection $my_processes;

    public function __construct()
    {
        $this->my_processes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTotalMemory(): ?int
    {
        return $this->total_memory;
    }

    public function setTotalMemory(?int $total_memory): static
    {
        $this->total_memory = $total_memory;

        return $this;
    }

    public function getTotalCore(): ?int
    {
        return $this->total_core;
    }

    public function setTotalCore(?int $total_core): static
    {
        $this->total_core = $total_core;

        return $this;
    }

    /**
     * @return Collection<int, Process>
     */
    public function getMyProcesses(): Collection
    {
        return $this->my_processes;
    }

    public function addMyProcess(Process $myProcess): static
    {
        if (!$this->my_processes->contains($myProcess)) {
            $this->my_processes->add($myProcess);
            $myProcess->setMyMachine($this);
        }

        return $this;
    }

    public function removeMyProcess(Process $myProcess): static
    {
        if ($this->my_processes->removeElement($myProcess)) {
            
            if ($myProcess->getMyMachine() === $this) {
                $myProcess->setMyMachine(null);
            }
        }

        return $this;
    }

    private function balancer(): ?int
    {
        $myProcesses = $this->getMyProcesses()->toArray();
        $currentMemoryUsage = array_sum(array_map(fn($p) => $p->getTotalMemory(), $myProcesses));
        $currentCpuUsage = array_sum(array_map(fn($p) => $p->getTotalCore(), $myProcesses));

        return $this->total_memory- $currentMemoryUsage - $currentCpuUsage + $this->total_core;
    }
}
