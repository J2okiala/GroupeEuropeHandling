<?php

namespace App\Form;

use App\Entity\OffreEmploi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostezOffreEmploiFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('poste', ChoiceType::class, [
                'choices' => [
                    'Agent de piste' => 'Agent de piste',
                    'Agent de trafic' => 'Agent de trafic',
                    'Manutentionnaire' => 'Manutentionnaire',
                    'Agent escale' => 'Agent escale',
                    'Agent cargo' => 'Agent cargo',
                    'Bagagiste' => 'Bagagiste',
                    'Agent livraison' => 'Agent livraison',
                ],
                'label' => 'Poste',
            ])
            ->add('typeContrat', ChoiceType::class, [
                'choices' => [
                    'CDI' => 'CDI',
                    'CDD' => 'CDD',
                ],
                'label' => 'Type de contrat',
            ])
            ->add('descriptionPoste', TextareaType::class, [
                'label' => 'Description du poste',
            ])
            ->add('modaliteTravail', ChoiceType::class, [
                'choices' => [
                    'temps pleins' => 'temps pleins',
                    'temps partiel' => 'temps partiel',
                ],
                'label' => 'Modalité de travail',
            ])
            ->add('localisation', ChoiceType::class, [
                'choices' => [
                    'Roissy' => 'Roissy',
                    'Orly' => 'Orly',
                ],
                'label' => 'Localisation',
            ])
            ->add(
                'Postez',
                SubmitType::class,
                ["attr" => ["class" => "btn btn-primary"]]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OffreEmploi::class,
            'csrf_protection' => true, // Activer la protection CSRF
        ]);
    }
}
