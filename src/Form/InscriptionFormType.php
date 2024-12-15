<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class InscriptionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add(
            'civilite',
            ChoiceType::class,
            [
                'label' => 'civilite',
                'choices' => [
                    'Sélectionnez votre civilite' => null,
                    'homme' => 'homme',
                    'femme' => 'femme',
                ],
                'placeholder' => false, // Ne pas ajouter de placeholder par défaut
                'required' => true,
                'constraints' => [
                    new NotBlank(["message" => "La civilité est obligatoire"]),
                ],
                'expanded' => false, // Utiliser un menu déroulant
                'multiple' => false, // Un choix unique
            ]
        )
            ->add(
                'nom',
                TextType::class,
                [
                    'label' => 'nom',
                    'attr' => ['placeholder' => "Entrez votre nom"],
                    'required' => true,
                    'constraints' => [
                        new NotBlank(["message" => "Le nom est obligatoire"]),
                        new Length([
                            'min' => 3,
                            'minMessage' => "Le nom doit contenir au moins {{ limit }} caractères",
                            'max' => 30,
                            'maxMessage' => "Le nom doit contenir au maximum {{ limit }} caractères",
                        ]),
                    ],
                ])
            ->add(
                'prenom',
                TextType::class,
                [
                    'label' => 'prenom',
                    'attr' => ['placeholder' => "Entrez votre prenom"],
                    'required' => true,
                    'constraints' => [
                        new NotBlank(["message" => "Le prenom est obligatoire"]),
                        new Length([
                            'min' => 3,
                            'minMessage' => "Le prenom doit contenir au moins {{ limit }} caractères",
                            'max' => 30,
                            'maxMessage' => "Le prenom doit contenir au maximum {{ limit }} caractères",
                        ]),
                    ],
                ])
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Email',
                    'attr' => ['placeholder' => "Entrez votre email"],
                    'required' => true,
                    'constraints' => [
                        new NotBlank(["message" => "L'email est obligatoire"]),
                        new Email(["message" => "L'email est invalide"]),
                    ],
                ])
            ->add(
                'password',
                PasswordType::class,
                [
                    'label' => 'Mot de passe',
                    'attr' => ['placeholder' => "Entrez votre mot de passe"],
                    'required' => true,
                    'constraints' => [
                        new NotBlank(["message" => "Le mot de passe est obligatoire"]),
                        new Length([
                            'min' => 8,
                            'minMessage' => "Le mot de passe doit contenir au moins {{ limit }} caractères",
                            'max' => 255,
                        ]),
                    ],
                ])
            ->add(
                'confirmPassword',
                PasswordType::class,
                [
                    'label' => 'Confirmez le mot de passe',
                    'attr' => ['placeholder' => "Confirmez votre mot de passe"],
                    'mapped' => false, // Ce champ n'est pas mappé à l'entité
                    'constraints' => [
                        new NotBlank(["message" => "La confirmation du mot de passe est obligatoire"]),
                        new Length([
                            'min' => 6,
                            'minMessage' => "Le mot de passe doit contenir au moins {{ limit }} caractères",
                            'max' => 255,
                        ]),
                        new Callback([$this, 'validatePasswordMatch'])
                    ],
                ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Candidat' => 'ROLE_CANDIDAT',
                    'Employeur' => 'ROLE_EMPLOYEUR',
                ],
                'label' => 'Rôle',
                'expanded' => true, // Affiche des boutons radio
                'multiple' => false, // Un seul rôle sélectionnable
                'mapped' => false, // Important : ne pas mapper directement à une propriété, nous le gérons dans la route
                'data' => 'ROLE_CANDIDAT', // Valeur par défaut si aucun rôle n'est sélectionné
            ])
            ->add(
                'Inscription',
                SubmitType::class,
                ["attr" => ["class" => "btn btn-primary"]]
            );
    }

    // Validation callback pour vérifier la correspondance des mots de passe
    public function validatePasswordMatch($value, ExecutionContextInterface $context): void
    {
        $form = $context->getRoot(); // On récupère le formulaire complet
        $password = $form->get('password')->getData();
        $confirmPassword = $form->get('confirmPassword')->getData();

        if ($password !== $confirmPassword) {
            // Ajoute une erreur de validation
            $context->buildViolation('Les mots de passe ne correspondent pas.')
                ->atPath('confirmPassword')
                ->addViolation();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
