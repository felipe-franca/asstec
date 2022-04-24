<?php

namespace App\Controller;

use App\Entity\Tickets;
use App\Controller\DefaultController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketsController extends DefaultController
{
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
}
