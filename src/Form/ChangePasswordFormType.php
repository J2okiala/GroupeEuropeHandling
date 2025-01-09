<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password', // Aurocomplétion désactivée pour le nouveaux mot de pass
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer un mot de passe', // Message : Veuillez entrer un mot de passe
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                            'max' => 255,
                            'maxMessage' => "Le mot de passe ne doit pas dépasser {{ limit }} caractères.",
                        ]),
                        new PasswordStrength(), // Vérifie la complexité du mot de passe
                        new NotCompromisedPassword(), // Vérifie si le mot de passe n'est pas compromis (apparaît dans des bases de données piratées)
                    ],
                    'label' => 'Nouveau mot de passe',
                ],
                'second_options' => [
                    'label' => 'Répétez le mot de passe',
                ],
                'invalid_message' => 'Les champs de mot de passe doivent correspondre.', // Message : Les champs de mot de passe doivent correspondre
                // Au lieu d'être directement défini dans l'objet,
                // ce mot de passe est lu et encodé dans le contrôleur
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true, // Activer la protection CSRF
        ]);

    }
}
