<?php

namespace App\Controller;

use App\Controller\DefaultController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ClientUser;
use App\Form\NewClientType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientController extends DefaultController
{
    #[Route('/cli', name: 'customers')]
    #[IsGranted('ROLE_ADMIN')]
    public function listCustomers(Request $request): Response
    {
        $em = $this->doctrine->getManager();

        $clients = $em->getRepository(ClientUser::class)->listClients();

        return $this->render('users/index.html.twig', [
            'userEntity' => $clients,
            'label'      => 'client',
            'total'      => count($clients)
        ]);
    }

    #[Route('/cli/novo', name: 'customer_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function newCustomer(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $em = $this->doctrine->getManager();

        $client = new ClientUser();
        $form = $this->createForm(NewClientType::class, $client);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $newClient = $form->getData();

                $hashedPassword = $userPasswordHasher->hashPassword(
                    $newClient,
                    'help@tech'
                );

                $client->setPassword($hashedPassword);
                $client->setRoles(['ROLE_USER']);

                $em->persist($newClient);
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('customer_new');
            }

            $this->addFlash('success', 'Cliente cadastrado com sucesso !');
            return $this->redirectToRoute('customers');
        }

        return $this->render('forms/new_client.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/cli/delete/{id}', name: 'customer_delete')]
    public function removeCustomer(Request $request, ClientUser $user): Response
    {
        $em = $this->doctrine->getManager();
        try {
            $em->getRepository(ClientUser::class)->remove($user);
            $this->addFlash('success', 'Cliente removido com sucesso !');
        } catch(\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('customers');
    }

    #[Route('/cli/editar/{id}', name: 'customer_edit')]
    public function editCustomer(Request $request, ClientUser $user): Response
    {
        $em = $this->doctrine->getManager();

        $form = $this->createForm(NewClientType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $editedClient = $form->getData();

                $em->persist($editedClient);
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('customer_new');
            }

            $this->addFlash('success', 'Cliente atualizado com sucesso !');
            return $this->redirectToRoute('customers');
        }

        return $this->render('forms/edit_client.html.twig', [
            'form'   => $form->createView(),
            'user'   => $user
        ]);
    }
}