<?php

namespace App\Form;

use App\Entity\Tickets;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class TicketsFinishType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('client', TextType::class, [
                'label'    => 'Aberto Por:',
                'attr'     => ['class' => 'form-control mb-3'],
                'disabled' => true,
                'data'     => $options['data']->getClient()->getUserName(),
            ])
            ->add('createdAt', TextType::class, [
                'label'    => 'Aberto Por:',
                'attr'     => ['class' => 'form-control mb-3 col'],
                'disabled' => true,
                'data'     => $options['data']->getCreatedAt()->format('d/m/Y H:i'),
            ])
            ->add('responsable', TextType::class, [
                'label'    => 'Responsavel: ',
                'attr'     => ['class' => 'form-control mb-3'],
                'disabled' => true,
                'data'     => $options['data']->getResponsable()->getUsername(),
            ])
            ->add('serviceStart', DateTimeType::class, [
                'label'          => 'Inicio do Atendimento: ',
                'attr'           => ['class' => 'mb-3'],
                'row_attr'       => ['class' => 'form-control mb-3'],
                'date_widget'    => 'single_text',
                'time_widget'    => 'single_text',
                'model_timezone' => 'America/Sao_Paulo',
                'view_timezone' => 'America/Sao_Paulo',
                'required'       => true,
            ])
            ->add('serviceEnd', DateTimeType::class, [
                'label'          => 'Inicio do Atendimento: ',
                'attr'           => ['class' => 'mb-3'],
                'row_attr'       => ['class' => 'form-control mb-3'],
                'date_widget'    => 'single_text',
                'time_widget'    => 'single_text',
                'model_timezone' => 'America/Sao_Paulo',
                'view_timezone'  => 'America/Sao_Paulo',
                'required'       => true,
            ])
            ->add('reason', TextareaType::class, [
                'label'    => 'Motivo: ',
                'attr'     => ['class' => 'form-control mb-3'],
                'disabled' => true,
            ])
            ->add('observation', TextareaType::class, [
                'label'    => 'Observações do Chamado',
                'attr'     => ['class' => 'form-control mb-3'],
                'disabled' => true,
            ])
            ->add('solution', TextareaType::class, [
                'label'    => 'Solução: ',
                'attr'     => ['class' => 'form-control mb-3'],
                'required' => true
            ])
            ->add('save', SubmitType::class, [
               'label' => 'Fechar Chamado',
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
