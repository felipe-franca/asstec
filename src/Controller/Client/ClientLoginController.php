<?php

namespace App\Controller\Client;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ClientLoginController extends AbstractController
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

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error ? $error->getMessageKey() : null,
            'isClient' => true
        ]);
    }
}
