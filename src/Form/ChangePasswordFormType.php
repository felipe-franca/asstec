<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Por favor insira uma senha',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Sua senha deve ter pelo menos {{ limit }} caracteres',
                            'max' => 4096,
                        ]),
                    ],
                    'label' => 'Nova senha',
                ],
                'second_options' => [
                    'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control mb-3'],
                    'label' => 'Repita a senha',
                ],
                'invalid_message' => 'Os campos de senha devem corresponder.',
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
