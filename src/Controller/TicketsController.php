<?php

namespace App\Controller;

use Exception;
use App\Entity\Tickets;
use App\Form\TicketOpenType;
use App\Form\TicketsFinishType;
use App\Form\TicketApprovalType;
use App\Controller\DefaultController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class TicketsController extends DefaultController
{
    #[Route('/chamados/abertos', name: 'app_tickets_opened')]
    public function opened(Request $request): Response
    {
        $em = $this->doctrine->getManager();
        $tickets = $em->getRepository(Tickets::class)->findByStatus(Tickets::STATUS_OPENED);

        return $this->render('tickets/index.html.twig', [
            'tickets'     => $tickets,
            'statusLabel' => Tickets::$statuses[Tickets::STATUS_OPENED],
            'status'      => Tickets::STATUS_OPENED,
            'total'       => count($tickets)
        ]);
    }

    #[Route('/chamados/fechados', name: 'app_tickets_closed')]
    public function closed(Request $request): Response
    {
        $em = $this->doctrine->getManager();
        $tickets = $em->getRepository(Tickets::class)->findByStatus(Tickets::STATUS_FINISHED);

        return $this->render('tickets/index.html.twig', [
            'tickets'     => $tickets,
            'statusLabel' => Tickets::$statuses[Tickets::STATUS_FINISHED],
            'status'      => Tickets::STATUS_FINISHED,
            'total'       => count($tickets)
        ]);
    }

    #[Route('/chamados/aguardando-aprovacao', name: 'app_tickets_waiting')]
    #[IsGranted('ROLE_ADMIN')]
    public function waiting(Request $request): Response
    {
        $em = $this->doctrine->getManager();
        $tickets = $em->getRepository(Tickets::class)->findByStatus(Tickets::STATUS_APPROVAL_PENDING);

        return $this->render('tickets/approval_pending.html.twig', [
            'tickets'     => $tickets,
            'statusLabel' => Tickets::$statuses[Tickets::STATUS_APPROVAL_PENDING],
            'status'      => Tickets::STATUS_APPROVAL_PENDING,
            'total'       => count($tickets)
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
            } catch(Exception $e) {
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
            } catch(Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('app_tickets_finish');
            }
        }

        return $this->render('forms/finish_ticket_form.html.twig', [
            'ticket' => $ticket,
            'form'   => $form->createView(),
        ]);
    }

    #[Route('/chamados/novo-chamado', name: 'app_ticket_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function openTicket(Request $request): Response
    {
        $em  = $this->doctrine->getManager();

        $ticket = new Tickets();
        $form = $this->createForm(TicketOpenType::class, $ticket);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $ticket = $form->getData();

                $ticket->setCreatedAt(new \DateTime('now'));
                $ticket->setUpdatedAt(new \DateTime('now'));
                $ticket->setStatus(Tickets::STATUS_OPENED);

                $em->persist($ticket);
                $em->flush();

                $this->addFlash('success', 'Chamado aberto com sucesso !');
                return $this->redirectToRoute('app_tickets_opened');
            } catch (Exception $e) {
                $this->addFlash('warning', 'Erro ao abrir chamado. Tente novamente.');
                return $this->redirectToRoute('app_ticket_new');
            }
        }

        return $this->render('forms/open_ticket_form.html.twig', [
            'ticket' => $ticket,
            'form'   => $form->createView(),
        ]);
    }

    #[Route('/chamdos/abertos/assumir/{id}', name: 'app_tickets_opened_assume')]
    public function assume(Request $request, Tickets $ticket) {
        $em = $this->doctrine->getManager();
        try {
            $ticket->setResponsable($this->getUser());
            $em->persist($ticket);
            $em->flush();

            $this->addFlash('success', 'Chamados assumido com sucesso !');
        } catch(Exception $e) {
            $this->addFlash('error', 'Ocorreu um erro ao assumiro o chamado. Tente novamente.');
        }

        return $this->redirectToRoute('app_tickets_opened');
    }

    #[Route('/chamados/exportar/{slug}', name: 'app_tickets_export_xls')]
    public function exportTickets(Request $request, string $slug): Response
    {
        $em  = $this->doctrine->getManager();
        $now = new \DateTime('now');

        $fileName = \str_replace([' '], '_',
            \sprintf( 'chamados_%s_%s.xlsx',
                \strtolower(Tickets::$statuses[$slug]),
                 $now->format('d_m_Y')
            )
        );

        $tickets = $em->getRepository(Tickets::class)->findByStatus($slug);

        $spreedsheet = new Spreadsheet();
        $sheet       = $spreedsheet->getActiveSheet();

        $sheet->setTitle('Chamados ' . Tickets::$statuses[$slug]);
        $sheet->setCellValue('A1', 'Chamados ' . Tickets::$statuses[$slug]);
        $sheet->setCellValue('C1', 'Exportado em:');
        $sheet->setCellValue('D1', $now->format('d/m/Y H:i'));

        $sheet->setCellValue('A3', 'Total: ' . count($tickets));

        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A3')->getFont()->setBold(true);

        $sheet->setCellValue('A5', 'Número');
        $sheet->setCellValue('B5', 'Cliente');
        $sheet->setCellValue('C5', 'Data Abertura');
        $sheet->setCellValue('D5', 'Responsável');
        $sheet->setCellValue('E5', 'Motivo');
        $sheet->setCellValue('F5', 'Observações');
        $sheet->setCellValue('G5', 'Fechado Em');
        $sheet->setCellValue('H5', 'Inicio do Serviço');
        $sheet->setCellValue('I5', 'Término do Serviço');
        $sheet->setCellValue('J5', 'Solução');

        $sheet->getStyle('A5:J5')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('C0C0C0');

        $headersFont = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ];

        $sheet->getStyle('A5:J5')->applyFromArray($headersFont);

        $row = 6;
        foreach ($tickets as $ticket) {
            $sheet->setCellValue('A' . $row, $ticket->getTicketNumber());
            $sheet->setCellValue('B' . $row, $ticket->getClient()->getUsername());
            $sheet->setCellValue('C' . $row, $ticket->getCreatedAt()->format('d/m/Y H:i'));
            $sheet->setCellValue('D' . $row, $ticket->getResponsable()->getUsername()?: '-');
            $sheet->setCellValue('E' . $row, $ticket->getReason()?: '-');
            $sheet->setCellValue('F' . $row, $ticket->getObservation()?: '-');
            $sheet->setCellValue('G' . $row, $ticket->getClosedAt()? $ticket->getClosedAt()->format('d/m/Y H:i') : '-' );
            $sheet->setCellValue('H' . $row, $ticket->getServiceStart()? $ticket->getServiceStart()->format('d/m/Y H:i') : '-' );
            $sheet->setCellValue('I' . $row, $ticket->getServiceEnd()? $ticket->getServiceEnd()->format('d/m/Y H:i') : '-' );
            $sheet->setCellValue('J' . $row, $ticket->getSolution()?: '-');
            $row++;
        }

        $grid = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000']
                ]
            ]
        ];

        $sheet->getStyle('A5:J' . ($row - 1))->applyFromArray($grid);

        $writer    = new Xlsx($spreedsheet);
        $temp_file = tempnam(\sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);

        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
