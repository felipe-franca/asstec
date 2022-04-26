<?php

namespace App\Controller;

use Exception;
use App\Entity\Tickets;
use App\Form\TicketsFinishType;
use App\Form\TicketApprovalType;
use App\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class TicketsController extends DefaultController
{
    #[Route('/chamados/abertos', name: 'app_opened')]
    public function opened(Request $request): Response
    {
        $em = $this->doctrine->getManager();
        $tickets = $em->getRepository(Tickets::class)->findByStatus(Tickets::STATUS_OPENED);

        return $this->render('tickets/index.html.twig', [
            'tickets' => $tickets,
            'status'  => Tickets::$statuses[Tickets::STATUS_OPENED],
            'total'   => count($tickets)
        ]);
    }

    #[Route('/chamados/fechados', name: 'app_tickets_closed')]
    public function closed(Request $request): Response
    {
        $em = $this->doctrine->getManager();
        $tickets = $em->getRepository(Tickets::class)->findByStatus(Tickets::STATUS_FINISHED);

        return $this->render('tickets/index.html.twig', [
            'tickets' => $tickets,
            'status'  => Tickets::$statuses[Tickets::STATUS_FINISHED],
            'total'   => count($tickets)
        ]);
    }

    #[Route('/chamados/aguardando-aprovacao', name: 'app_tickets_waiting')]
    #[IsGranted('ROLE_ADMIN')]
    public function waiting(Request $request): Response
    {
        $em = $this->doctrine->getManager();
        $tickets = $em->getRepository(Tickets::class)->findByStatus(Tickets::STATUS_APPROVAL_PENDING);

        return $this->render('tickets/approval_pending.html.twig', [
            'tickets' => $tickets,
            'status'  => Tickets::$statuses[Tickets::STATUS_APPROVAL_PENDING],
            'total'   => count($tickets)
        ]);
    }

    #[Route('/chamados/aprovar/{id}', name: 'app_ticket_approve')]
    #[IsGranted('ROLE_ADMIN')]
    public function approve(Request $request, Tickets $ticket): Response
    {
        $em = $this->doctrine->getManager();

        $form = $this->createForm(TicketApprovalType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $ticket = $form->getData();
                $ticket->setUpdatedAt(new \DateTime('now'));
                $ticket->setStatus(Tickets::STATUS_OPENED);

                $em->persist($ticket);
                $em->flush();

                $this->addFlash('success', 'Chamado aprovado com sucesso !');
                return $this->redirectToRoute('app_tickets_waiting');
            } catch(\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('app_ticket_approve');
            }
        }

        return $this->render('forms/approve.html.twig', [
            'ticket' => $ticket,
            'status' => Tickets::$statuses[Tickets::STATUS_APPROVAL_PENDING],
            'form'   => $form->createView(),
        ]);
    }

    #[Route('chamados/detalhes/{id}', name: 'app_tickets_details')]
    public function ticketDetails(Request $request, Tickets $ticket): Response
    {
        return $this->render('tickets/detail.html.twig', [
            'ticket' => $ticket
        ]);
    }

    #[Route('/chamados/finalizar/{id}', name: 'app_tickets_finish')]
    public function finish(Request $request, Tickets $ticket): Response
    {
        $em = $this->doctrine->getManager();

        $form = $this->createForm(TicketsFinishType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $ticket = $form->getData();

                $ticket->setStatus(Tickets::STATUS_FINISHED);
                $ticket->setClosedAt(new \DateTime('now'));

                $em->persist($ticket);
                $em->flush();

                $this->addFlash('success', 'Chamado encerrado com sucesso.');
                return $this->redirectToRoute('app_tickets_closed');
            } catch(\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('app_tickets_finish');
            }
        }

        return $this->render('forms/finish_ticket_form.html.twig', [
            'ticket' => $ticket,
            'form'   => $form->createView(),
        ]);
    }
}
