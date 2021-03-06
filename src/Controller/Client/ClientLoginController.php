<?php

namespace App\Controller\Client;

use App\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ClientLoginController extends DefaultController
{
    #[Route('/client', name: 'client_root')]
    public function root(Request $request): RedirectResponse
    {
        return $this->redirectToRoute('app_client_login');
    }

    #[Route('/client/login', name: 'app_client_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error)
            $this->addFlash('error', $error->getMessageKey());

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'isClient' => true
        ]);
    }
}
