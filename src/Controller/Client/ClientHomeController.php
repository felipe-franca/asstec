<?php

namespace App\Controller\Client;


use Exception;
use App\Entity\Tickets;
use App\Controller\DefaultController;
use App\Controller\HomeController;
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

        return $this->render('home/index.html.twig', [
            'openedData'   => $openedData,
            'closedData'   => $closedData,
            'monthlyData'  => $monthlyData,
        ]);
    }

    private function dailyOpenedData()
    {
        $em = $this->doctrine->getManager();
        $result = $em->getRepository(Tickets::class)->getDailyOpenedData($this->getUser());
        return empty($result) ? HomeController::mockResult() : $result;
    }

    private function dailyClosedData()
    {
        $em = $this->doctrine->getManager();
        $result =  $em->getRepository(Tickets::class)->getDailyClosedData($this->getUser());
        return empty($result) ? HomeController::mockResult() : $result;
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
