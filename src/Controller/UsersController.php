<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\NewUserType;
use App\Controller\DefaultController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersController extends DefaultController
{
    #[Route('/tecnicos', name: 'app_techs')]
    #[IsGranted('ROLE_ADMIN')]
    public function technical(Request $request): Response
    {
        $em = $this->doctrine->getManager();

        $techs = $em->getRepository(User::class)->listTechs();

        return $this->render('users/index.html.twig', [
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
                    'help@tech'
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

    #[Route('/tecnico/exportar', name: 'app_techs_export')]
    #[IsGranted('ROLE_ADMIN')]
    public function techsExport(Request $request): Response
    {
        return $this->spreadSheet('tech');
    }

    #[Route('/cli/exportar', name: 'app_clients_export')]
    #[IsGranted('ROLE_ADMIN')]
    public function clientsExport(Request $request): Response
    {
        return $this->spreadSheet('cli');
    }

    private function spreadSheet(string $userType): Response
    {
        $em  = $this->doctrine->getManager();
        $now = new \DateTime('now');

        $fileName = \str_replace([' '], '_',
            \sprintf( $userType == 'tech' ? 'tecnicos_%s.xlsx' : 'clientes_%s.xlsx',
                 $now->format('d_m_Y')
            )
        );

        $users = $userType == 'tech' ?
            $em->getRepository(User::class)->listTechs()
            : $em->getRepository(User::class)->listClients();

        $spreedsheet = new Spreadsheet();
        $sheet       = $spreedsheet->getActiveSheet();

        $sheet->setTitle($userType == 'tech' ? 'Técnicos' : 'Clientes');
        $sheet->setCellValue('A1', $userType == 'tech' ? 'Técnicos' : 'Clientes');
        $sheet->setCellValue('C1', 'Exportado em:');
        $sheet->setCellValue('D1', $now->format('d/m/Y H:i'));

        $sheet->setCellValue('A3', 'Total: ' . count($users));

        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A3')->getFont()->setBold(true);

        $sheet->setCellValue('A5', 'Número');
        $sheet->setCellValue('B5', 'Nome');
        $sheet->setCellValue('C5', 'Email');
        $sheet->setCellValue('D5', 'Endereço');
        $sheet->setCellValue('E5', 'Telefone');;

        $sheet->getStyle('A5:E5')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('C0C0C0');

        $headersFont = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ];

        $sheet->getStyle('A5:E5')->applyFromArray($headersFont);

        $row = 6;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user->getId());
            $sheet->setCellValue('B' . $row, $user->getUsername());
            $sheet->setCellValue('C' . $row, $user->getEmail());
            $sheet->setCellValue('D' . $row, $user->getAddress()->__toString()?: '-');
            $sheet->setCellValue('E' . $row, $user->getPhonesListString()?: '-');
            $row++;
        }

        $grid = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000']
                ]
            ]
        ];

        $sheet->getStyle('A5:E' . ($row - 1))->applyFromArray($grid);

        $writer    = new Xlsx($spreedsheet);
        $temp_file = tempnam(\sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);

        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
