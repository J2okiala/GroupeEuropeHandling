<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ConnexionFormType extends AbstractType
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
                'Connexion',
                SubmitType::class,
                ["attr" => ["class" => "btn btn-primary"]]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
