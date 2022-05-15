<?php

namespace App\Form;

use App\Entity\User;
use App\Form\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class NewUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email: ',
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'email@email.com'
                ]
            ])
            ->add('occupation', ChoiceType::class, [
                'label'       => 'Cargo: ',
                'choices'     => User::$rolesList,
                'attr'        => ['class' => 'form-select'],
                'placeholder' => 'Selecione o cargo...',
                'required'    => true,
            ])
            ->add('username', TextType::class, [
                'label' => 'Nome: ',
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'nome'
                ],
            ])
            ->add('address', AddressType::class, [
                'label' => 'Endereço',
                'attr'  => ['class' => 'row g-3 col-form-label']
                ])
            ->add('phone', CollectionType::class, [
                'label' => 'Telefones: ',
                'attr' => ['maxlength' => 15],
                'entry_type'    => PhoneType::class,
                'entry_options' => ['label' => false],
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Cadastrar',
                'attr'  => ['class' => 'btn bg-green font-white mt-2 float-end w-25 shadow']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => User::class,
            'allow_extra_fields' => true
        ]);
    }
}
