<?php

namespace App\Controller;

use App\Controller\DefaultController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ClientUser;

class ClientController extends DefaultController
{
    #[Route('/customers', name: 'app_clients')]
    #[IsGranted('ROLE_ADMIN')]
    public function clients(Request $request): Response
    {
        $em = $this->doctrine->getManager();

        $clients = $em->getRepository(ClientUser::class)->listClients();

        return $this->render('client/index.html.twig', [
            'userEntity' => $clients,
            'label'      => 'Cliente',
            'total'      => count($clients)
        ]);
    }

}