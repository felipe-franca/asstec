<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/opened', name: 'app_opened')]
    public function opend(Request $request): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/closed', name: 'app_closed')]
    public function closed(Request $request): Response
    {
        return new JsonResponse(['ok' => true]);
    }

    #[Route('/waiting', name: 'app_waiting')]
    #[IsGranted('ROLE_ADMIN')]
    public function waiting(Request $request): Response
    {
        return new JsonResponse(['ok' => true]);
    }

    #[Route('/clients', name: 'app_clients')]
    #[IsGranted('ROLE_ADMIN')]
    public function clients(Request $request): Response
    {
        return new JsonResponse(['ok' => true]);
    }

    #[Route('/technical', name: 'app_techs')]
    #[IsGranted('ROLE_ADMIN')]
    public function technical(Request $request): Response
    {
        return new JsonResponse(['ok' => true]);
    }
}
