<?php

namespace App\Controller\Client;

use Exception;
use App\Entity\Tickets;
use App\Form\ClientTicketOpenType;
use App\Controller\DefaultController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ClientTicketsController extends DefaultController
{
    #[Route('/client/chamados', name: 'app_client_tickets')]
    public function index(): Response
    {
        $em = $this->doctrine->getManager();

        $tickets = $em->getRepository(Tickets::class)->listByClient($this->getUser());

        return $this->render('client_tickets/index.html.twig', [
            'tickets' => $tickets,
            'total'   => count($tickets)
        ]);
    }

    #[Route('/client/chamados/novo-chamado', name: 'app_client_ticket_new')]
    public function openTicket(Request $request): Response
    {
        $em  = $this->doctrine->getManager();

        $ticket = new Tickets();
        $form = $this->createForm(ClientTicketOpenType::class, $ticket);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $ticket = $form->getData();

                $ticket->setCreatedAt(new \DateTime('now'));
                $ticket->setUpdatedAt(new \DateTime('now'));
                $ticket->setClient($this->getUser());
                $ticket->setStatus(Tickets::STATUS_APPROVAL_PENDING);

                $em->persist($ticket);
                $em->flush();

                $this->addFlash('success', 'Chamado aberto com sucesso !');
                return $this->redirectToRoute('app_client_tickets');
            } catch (Exception $e) {
                $this->addFlash('warning', 'Erro ao abrir chamado. Tente novamente.');
                return $this->redirectToRoute('app_client_ticket_new');
            }
        }

        return $this->render('forms/open_ticket_form.html.twig', [
            'ticket'   => $ticket,
            'form'     => $form->createView(),
            'isClient' => true
        ]);
    }

    #[Route('/client/chamados/detalhes/{id}', name: 'app_client_tickets_details')]
    public function ticketDetails(Request $request, Tickets $ticket): Response
    {
        return $this->render('tickets/detail.html.twig', [
            'ticket' => $ticket
        ]);
    }

    #[Route('/client/chamados/exportar', name: 'app_client_tickets_export_xls')]
    public function exportTickets(Request $request): Response
    {
        $em  = $this->doctrine->getManager();
        $now = new \DateTime('now');

        $fileName = \str_replace([' '], '_',
            \sprintf( 'meus_chamados_%s.xlsx',
                 $now->format('d_m_Y')
            )
        );

        $tickets = $em->getRepository(Tickets::class)->findByClient($this->getUser());

        $spreedsheet = new Spreadsheet();
        $sheet       = $spreedsheet->getActiveSheet();

        $sheet->setTitle('Meus Chamados');
        $sheet->setCellValue('A1', 'Meus Chamados');
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
            $sheet->setCellValue('D' . $row, $ticket->getResponsable()? $ticket->getResponsable()->getUsername(): '-');
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
