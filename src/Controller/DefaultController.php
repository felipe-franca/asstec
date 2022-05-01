<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class DefaultController extends AbstractController
{
    protected $doctrine;
    protected $context;

    public function __construct(ManagerRegistry $doctrine, Security $context)
    {
        $this->doctrine = $doctrine;
        $this->context = $context;
    }
}