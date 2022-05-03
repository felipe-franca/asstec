<?php

namespace App\Form;

use App\Entity\Tickets;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ClientTicketOpenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ticketNumber', TextType::class, [
                'label'    => 'Número do Chamado',
                'attr'     => ['class' => 'form-control mb-3'],
                'disabled' => true
            ])
            ->add('reason', TextareaType::class, [
                'label'    => 'Motivo: ',
                'attr'     => ['class' => 'form-control mb-3'],
                'required' => true,
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
