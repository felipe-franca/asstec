<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\DefaultController;
use App\Entity\User;

class UsersController extends DefaultController
{
    #[Route('/technicians', name: 'app_techs')]
    #[IsGranted('ROLE_ADMIN')]
    public function technical(Request $request): Response
    {
        $em = $this->doctrine->getManager();

        $techs = $em->getRepository(User::class)->listTechs();

        return $this->render('client/index.html.twig', [
            'userEntity' => $techs,
            'label'      => 'Técnico',
            'total'      => count($techs)
        ]);
    }
}
