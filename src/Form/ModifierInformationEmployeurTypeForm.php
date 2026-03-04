<?php

namespace App\Form;

use App\Entity\Employeur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ModifierInformationEmployeurTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
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
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => "Entrez votre prénom"],
                'required' => true,
                'constraints' => [
                    new NotBlank(["message" => "Le prénom est obligatoire"]),
                    new Length([
                        'min' => 3,
                        'minMessage' => "Le prénom doit contenir au moins {{ limit }} caractères",
                        'max' => 30,
                        'maxMessage' => "Le prénom doit contenir au maximum {{ limit }} caractères",
                    ]),
                ],
            ])
            ->add('entreprise', TextType::class, [
                'label' => 'entreprise',
                'attr' => ['placeholder' => "Entrez votre nomEntreprise"],
                'required' => true,
                'constraints' => [
                    new NotBlank(["message" => "entreprise est obligatoire"]),
                    new Length([
                        'min' => 3,
                        'minMessage' => "Le nomEntreprise doit contenir au moins {{ limit }} caractères",
                        'max' => 30,
                        'maxMessage' => "Le nomEntreprise doit contenir au maximum {{ limit }} caractères",
                    ]),
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Utilisez uniquement Candidat::class, pas Utilisateur::class
        $resolver->setDefaults([
            'data_class' => Employeur::class,
            'csrf_protection' => true, // Activer la protection CSRF
        ]);
    }
}