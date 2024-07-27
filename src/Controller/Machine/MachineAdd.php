<?php

namespace App\Controller\Machine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MachineAdd extends AbstractController
{
    #[Route('/machine/add', name: "add_machine")]
    public function index()
    {
        return $this->render("Machine/add.html.twig");
    }
}