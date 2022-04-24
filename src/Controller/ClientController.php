<?php

namespace App\Controller;

use App\Controller\DefaultController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ClientUser;
use App\Form\NewClientType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientController extends DefaultController
{
    #[Route('/customers', name: 'customers')]
    #[IsGranted('ROLE_ADMIN')]
    public function clients(Request $request): Response
    {
        $em = $this->doctrine->getManager();

        $clients = $em->getRepository(ClientUser::class)->listClients();

        return $this->render('client/index.html.twig', [
            'userEntity' => $clients,
            'label'      => 'client',
            'total'      => count($clients)
        ]);
    }

    #[Route('/customer/new', name: 'customer_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function newCustomer(Request $request, UserPasswordHasherInterface $userPasswordHasher)
    {
        $em = $this->doctrine->getManager();

        $client = new ClientUser();
        $form = $this->createForm(NewClientType::class, $client);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newClient = $form->getData();

            $hashedPassword = $userPasswordHasher->hashPassword(
                $newClient,
                'wpsbrasil'
            );

            $client->setPassword($hashedPassword);

            $em->persist($newClient);
            $em->flush();

            return $this->redirectToRoute('customers');
        }

        return $this->render('forms/new_client.html.twig', [
            'form' => $form->createView()
        ]);
    }

}