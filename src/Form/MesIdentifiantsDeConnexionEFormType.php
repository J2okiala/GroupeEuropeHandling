<?php

namespace App\Form;

use App\Entity\Employeur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MesIdentifiantsDeConnexionEFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Email',
                    'attr' => ['placeholder' => "Entrez votre email"],
                    'mapped' => false, // Non mappé à Employeur
                    'required' => true,
                    'constraints' => [
                        new NotBlank(["message" => "L'email est obligatoire"]),
                        new Email(["message" => "L'email est invalide"]),
                    ],
                ]
            )
            ->add(
                'confirmEmail',
                EmailType::class,
                [
                    'label' => 'Confirmez votre email',
                    'attr' => ['placeholder' => "Confirmez votre email"],
                    'mapped' => false,
                    'constraints' => [
                        new NotBlank(["message" => "La confirmation de l'email est obligatoire"]),
                        new Email(["message" => "L'email est invalide"]),
                    ],
                ]
            )
            ->add(
                'password',
                PasswordType::class,
                [
                    'label' => 'Mot de passe',
                    'attr' => ['placeholder' => "Entrez votre mot de passe"],
                    'mapped' => false, // Non mappé à Employeur
                    'required' => false,
                    'constraints' => [
                        new Length([
                            'min' => 8,
                            'minMessage' => "Le mot de passe doit contenir au moins {{ limit }} caractères",
                            'max' => 255,
                        ]),
                    ],
                ]
            )
            ->add(
                'confirmPassword',
                PasswordType::class,
                [
                    'label' => 'Confirmez votre mot de passe',
                    'attr' => ['placeholder' => "Confirmez votre mot de passe"],
                    'mapped' => false,
                    'required' => false,
                ]
            )
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $email = $form->get('email')->getData();
                $confirmEmail = $form->get('confirmEmail')->getData();
                $password = $form->get('password')->getData();
                $confirmPassword = $form->get('confirmPassword')->getData();

                if ($email !== $confirmEmail) {
                    $form->get('confirmEmail')->addError(new FormError("Les emails ne correspondent pas."));
                }

                if (!empty($password) && $password !== $confirmPassword) {
                    $form->get('confirmPassword')->addError(new FormError("Les mots de passe ne correspondent pas."));
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employeur::class, // Le formulaire est basé sur Employeur
        ]);
    }
}
