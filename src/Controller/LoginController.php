<?php

namespace App\Controller;


use App\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends DefaultController
{
    #[Route('/', name: 'root')]
    public function root(): RedirectResponse
    {
        if ($this->getUser()) {
            return  $this->context->isGranted('ROLE_ADMIN')
                ? $this->redirectToRoute('app_home')
                : $this->redirectToRoute('app_tickets_opened');
        }

        return $this->redirectToRoute('login');
    }


    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error ? $error->getMessageKey() : null,
            'isClient' => false
        ]);
    }
}
