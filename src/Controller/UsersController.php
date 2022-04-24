<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\DefaultController;
use App\Entity\User;
use App\Form\NewUserType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersController extends DefaultController
{
    #[Route('/tecnicos', name: 'app_techs')]
    #[IsGranted('ROLE_ADMIN')]
    public function technical(Request $request): Response
    {
        $em = $this->doctrine->getManager();

        $techs = $em->getRepository(User::class)->listTechs();

        return $this->render('datatable/index.html.twig', [
            'userEntity' => $techs,
            'label'      => 'techs',
            'total'      => count($techs)
        ]);
    }

    #[Route('/tecnicos/novo', name: 'app_techs_new')]
    public function newTech(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $em = $this->doctrine->getManager();

        $user = new User();
        $form = $this->createForm(NewUserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $newUser = $form->getData();
                $newUser->setRoles(
                    $newUser->getOccupation() == 'ROLE_ADMIN' ? ['ROLE_ADMIN'] : ['ROLE_ANALIST']
                );

                $hashedPassword = $userPasswordHasher->hashPassword(
                    $newUser,
                    'wpsbrasil'
                );
                $newUser->setPassword($hashedPassword);

                $em->persist($newUser);
                $em->flush();

            } catch(\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('app_techs_new');
            }

            $this->addFlash('success', 'Usuario cadastrado com sucesso !');
            return $this->redirectToRoute('app_techs');
        }

        return $this->render('forms/new_user.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/tecnicos/delete/{id}', name: 'app_techs_delete')]
    public function removeTech(Request $request, User $user): Response
    {
        $em = $this->doctrine->getManager();
        try {
            $em->getRepository(User::class)->remove($user);
            $this->addFlash('success', 'Usuário removido com sucesso !');
        } catch(\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('app_techs');
    }

    #[Route('/tecnico/editar/{id}', name: 'app_techs_edit')]
    public function editTech(Request $request, User $user): Response
    {
        $em = $this->doctrine->getManager();

        $form = $this->createForm(NewUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $newUser = $form->getData();
                $newUser->setRoles(
                    $newUser->getOccupation() == 'ROLE_ADMIN' ? ['ROLE_ADMIN'] : ['ROLE_ANALIST']
                );

                $em->persist($newUser);
                $em->flush();

            } catch(\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('app_techs_edit');
            }

            $this->addFlash('success', 'Usuario editado com sucesso !');
            return $this->redirectToRoute('app_techs');
        }

        return $this->render('forms/edit_user.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
