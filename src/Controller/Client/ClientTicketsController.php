<?php

namespace App\Controller;

use App\Entity\Tickets;
use App\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientTicketsController extends DefaultController
{
    #[Route('/client/chamados', name: 'app_client_tickets')]
    public function index(): Response
    {
        $em = $this->doctrine->getManager();

        $tickets = $em->getRepository(Tickets::class)->listByClient($this->getUser());

        return $this->render('client_tickets/index.html.twig', [
            'tickets' => $tickets
        ]);
    }
}
