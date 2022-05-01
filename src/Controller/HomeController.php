<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Tickets;
use App\Controller\Helper;
use App\Controller\DefaultController;
use App\Form\TicketOpenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class HomeController extends DefaultController
{

    #[Route('/home', name: 'app_home')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request): Response
    {
        $openedData = $this->dailyOpenedData();
        $closedData = $this->dailyClosedData();
        $monthlyData = $this->monthlyData();
        $now = new \DateTime('now');

        dump($monthlyData);

        return $this->render('home/index.html.twig', [
            'openedData'   => $openedData,
            'closedData'   => $closedData,
            'monthlyData'  => $monthlyData,
            'currentMonth' => $now->format('m'),
        ]);
    }

    private function dailyOpenedData()
    {
        $em = $this->doctrine->getManager();
        return $em->getRepository(Tickets::class)->getDailyOpenedData();
    }

    private function dailyClosedData()
    {
        $em = $this->doctrine->getManager();
        return $em->getRepository(Tickets::class)->getDailyClosedData();
    }

    private function monthlyData()
    {
        $em = $this->doctrine->getManager();
        $opened = $em->getRepository(Tickets::class)->getMonthlyOpenedData();
        $closed = $em->getRepository(Tickets::class)->getMonthlyClosedData();
        $measure = array_merge($opened, $closed);
        dump($measure);

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
}
