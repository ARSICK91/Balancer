<?php

namespace App\Entity;

use App\Repository\ProcessRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProcessRepository::class)]
class Process
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $need_memory = null;

    #[ORM\Column(nullable: true)]
    private ?int $need_core = null;

    #[ORM\ManyToOne(inversedBy: 'my_processes')]
    private ?Machine $my_machine = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNeedMemory(): ?int
    {
        return $this->need_memory;
    }

    public function setNeedMemory(?int $need_memory): static
    {
        $this->need_memory = $need_memory;

        return $this;
    }

    public function getNeedCore(): ?int
    {
        return $this->need_core;
    }

    public function setNeedCore(?int $need_core): static
    {
        $this->need_core = $need_core;

        return $this;
    }

    public function getMyMachine(): ?Machine
    {
        return $this->my_machine;
    }

    public function setMyMachine(?Machine $my_machine): static
    {
        $this->my_machine = $my_machine;

        return $this;
    }
    public function delMyMachine(): static
    {
        $this->my_machine = null;
        return $this;
    }
    public function balance(): ?int
    {
        return $this->need_memory - $this->need_core;
    }
}
