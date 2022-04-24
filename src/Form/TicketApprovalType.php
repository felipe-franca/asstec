<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Tickets;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TicketApprovalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $ticket = $options['data'];
        $createdAt = $ticket->getCreatedAt()->format('d/m/Y');

        $builder
            ->add('createdAt', TextType::class, [
                'label'    => 'Aberto Em: ',
                'attr'     => ['class' => 'form-control mb-2'],
                'disabled' => true,
                'data'     => $createdAt
            ])
            ->add('ticketNumber', TextType::class, [
                'label'    => 'Número do Chamado:',
                'attr'     => ['class' => 'form-control mb-2'],
                'disabled' => true
            ])
            ->add('reason', TextareaType::class, [
                'label'    => 'Motivo: ',
                'disabled' => true,
                'attr'     => ['class' => 'form-control mb-2']
            ])
            ->add('observation', TextareaType::class, [
                'label' => 'Observações: ',
                'attr'  => ['class' => 'form-control mb-2']
            ])
            ->add('responsable', EntityType::class, [
                'label'         => 'Atribuir à:',
                'class'         => User::class,
                'required'      => true,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.occupation = :occupation')
                        ->setParameter('occupation', User::TECH_OCCUPATION)
                        ->orderBy('u.username', 'ASC');
                },
                'choice_label'  => 'username',
                'attr'          => ['class' => 'form-select mb-2'],
                'placeholder'   => 'Selecione um técnico ...'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Aprovar',
                'attr'  => ['class' => 'btn bg-green font-white mt-4 float-end w-25 shadow']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tickets::class,
        ]);
    }
}
