<?php

namespace App\Controller\Client;


use Exception;
use App\Entity\Tickets;
use App\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientHomeController extends DefaultController
{
    #[Route('/client/home', name: 'app_client_home')]
    public function index(Request $request): Response
    {
        $openedData = $this->dailyOpenedData();
        $closedData = $this->dailyClosedData();
        $monthlyData = $this->monthlyData();

        dump($openedData, $closedData, $monthlyData);

        return $this->render('home/index.html.twig', [
            'openedData'   => $openedData,
            'closedData'   => $closedData,
            'monthlyData'  => $monthlyData,
        ]);
    }

    private function dailyOpenedData()
    {
        $em = $this->doctrine->getManager();
        return $em->getRepository(Tickets::class)->getDailyOpenedData($this->getUser());
    }

    private function dailyClosedData()
    {
        $em = $this->doctrine->getManager();
        $result =  $em->getRepository(Tickets::class)->getDailyClosedData($this->getUser());

        // TODO: handle empty results. line 39
        if (empty($result)) {
            $now = new \DateTime('now');
            $result = [
                'Dia' => $now->format('d'),
                'Quantidade' => 0,
            ];
        }

        return array($result);
    }

    private function monthlyData()
    {
        $em = $this->doctrine->getManager();
        $opened = $em->getRepository(Tickets::class)->getMonthlyOpenedData($this->getUser());
        $closed = $em->getRepository(Tickets::class)->getMonthlyClosedData($this->getUser());
        $measure = array_merge($opened, $closed);

        $data = [];
        foreach(range(1,12) as $month) {
            $totalOpened = 0;
            $totalFinished = 0;

            foreach($measure as $item) {
                if ($item['months'] == $month) {
                    if ($item['status'] == Tickets::STATUS_FINISHED)
                        $totalFinished += $item['qnty'];
                }
            }

            foreach($measure as $item) {
                if($item['months'] == $month) {
                    if ($item['status'] == Tickets::STATUS_OPENED) {
                        $totalOpened += $item['qnty'];
                    }
                }
            }

            array_push($data, ['month' => $month, 'finished' => $totalFinished, 'opened' => $totalOpened]);
        }

        return $data;
    }

    #[Route('/client/chamados/detalhes/{id}', name: 'app_client_tickets_details')]
    public function ticketDetails(Request $request, Tickets $ticket): Response
    {
        return $this->render('tickets/detail.html.twig', [
            'ticket' => $ticket
        ]);
    }
}
