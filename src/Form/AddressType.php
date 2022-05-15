<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', TextType::class, [
                'label'      => 'Rua\Av.: ',
                'label_attr' => ['class' => 'form-label'],
                'required'   => true,
                'attr'     => [
                    'placeholder' => 'Rua\Av. A',
                    'class'       => 'form-control'
                ],
            ])
            ->add('number', IntegerType::class, [
                'label'    => 'Número: ',
                'required' => true,
                'attr'     => [
                    'autocomplete' => 'off',
                    'class'        => 'form-control',
                    'placeholder'  => '10'
                ]
            ])
            ->add('district', TextType::class, [
                'label'    => 'Bairro: ',
                'required' => true,
                'attr'     => [
                    'autocomplete' => 'off',
                    'class'        => 'form-control',
                    'placeholder'  => 'Bairro'
                ]
            ])
            ->add('uf', TextType::class, [
                'label'    => 'UF: ',
                'required' => true,
                'attr'     => [
                    'autocomplete' => 'off',
                    'class'        => 'form-control',
                    'placeholder'  => 'SP',
                    'maxlength'    => '2',
                    'pattern'      => '[A-Za-z]{2}'
                ]
            ])
            ->add('postal_code', TextType::class, [
                'label' => 'CEP: ',
                'required' => true,
                'attr' => [
                    'autocomplet' => 'off',
                    'class'       => 'form-control',
                    'placeholder' => '12345089',
                    'maxlength'   => '8',
                    'pattern'     => '[0-9]{8}'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
