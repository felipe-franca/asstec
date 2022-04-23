<?php

namespace App\Controller;

use Doctrine\Migrations\Version\State;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tickets;
class AppController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;

    }

    #[Route('/home', name: 'app_home')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/opened', name: 'app_opened')]
    public function opened(Request $request): Response
    {
        $em = $this->doctrine->getManager();
        $tickets = $em->getRepository(Tickets::class)->findByStatus(Tickets::STATUS_OPENED);

        return $this->render('opened/index.html.twig', [
            'tickets' => $tickets,
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

    #[Route('/admin/clients', name: 'app_clients')]
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
