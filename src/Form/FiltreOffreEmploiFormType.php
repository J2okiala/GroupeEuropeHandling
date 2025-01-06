<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreOffreEmploiFormType extends AbstractType
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
                'required' => false,
                'placeholder' => 'Tous les postes',
            ])
            ->add('typeContrat', ChoiceType::class, [
                'choices' => [
                    'CDI' => 'CDI',
                    'CDD' => 'CDD',
                ],
                'label' => 'Type de contrat',
                'required' => false,
                'placeholder' => 'Tous les types',
            ])
            ->add('modaliteTravail', ChoiceType::class, [
                'choices' => [
                    'Temps pleins' => 'temps pleins',
                    'Temps partiel' => 'temps partiel',
                ],
                'label' => 'Modalité de travail',
                'required' => false,
                'placeholder' => 'Toutes les modalités',
            ])
            ->add('localisation', ChoiceType::class, [
                'choices' => [
                    'Roissy' => 'Roissy',
                    'Orly' => 'Orly',
                ],
                'label' => 'Localisation',
                'required' => false,
                'placeholder' => 'Toutes les localisations',
            ])
            ->add('Rechercher', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET', 
            'csrf_protection' => false, // Désactivé pour les formulaires de recherche
        ]);        
    }
}
