<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Tickets;
use App\Entity\ClientUser;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TicketOpenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ticketNumber', TextType::class, [
                'label'    => 'Número do Chamado: ',
                'attr'     => ['class' => 'form-control mb-3'],
                'disabled' => true,
            ])
            ->add('client', EntityType::class, [
                'label' => 'Cliente: ',
                'class'         => ClientUser::class,
                'required'      => true,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.username', 'ASC');
                },
                'choice_label'  => 'username',
                'attr'          => ['class' => 'form-select mb-2'],
                'placeholder'   => 'Selecione um cliente ...'
            ])
            ->add('responsable', EntityType::class, [
                'label'         => 'Atribuir a: ',
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
            ->add('reason', TextareaType::class, [
                'label'    => 'Motivo: ',
                'attr'     => ['class' => 'form-control mb-3'],
                'required' => true,
            ])
            ->add('observation', TextareaType::class, [
                'label'    => 'Observações: ',
                'attr'     => ['class' => 'form-control mb-3'],
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Abrir',
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
