<?php

namespace App\Form;

use App\Entity\ClientUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\AddressType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NewClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email:',
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'email@email.com'
                ]
            ])
            ->add('username', TextType::class, [
               'label' => 'Nome: ',
               'attr'  => ['class' => 'form-control']
            ])
            ->add('address', AddressType::class, [
                'label' => 'Endereço',
                'attr'  => ['class' => 'row g-3 col-form-label']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Cadastrar',
                'attr' => [
                    'class' => 'btn bg-green font-white'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ClientUser::class,
        ]);
    }
}
