<?php

namespace App\Form;

use App\Document\CandidatureSpontanee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidatureSpontaneeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cv', FileType::class, [
                'label' => 'Votre CV (PDF)',
                'mapped' => true,  // On mappe directement à la propriété 'cv' de l'entité
                'required' => true,
            ])
            ->add('lm', FileType::class, [
                'label' => 'Votre Lettre de Motivation (PDF)',
                'mapped' => true, // On mappe directement à la propriété 'lm' de l'entité
                'required' => true,
            ])
            ->add('poste', ChoiceType::class, [
                'label' => 'Poste souhaité',
                'choices' => [
                    'Agent de piste' => 'Agent de piste',
                    'Agent de trafic' => 'Agent de trafic',
                    'Manutentionnaire' => 'Manutentionnaire',
                    'Agent escale' => 'Agent escale',
                    'Agent cargo' => 'Agent cargo',
                    'Bagagiste' => 'Bagagiste',
                    'Agent livraison' => 'Agent livraison',
                ],
                'placeholder' => 'Sélectionnez un poste',
                'mapped' => true, // Mapping du poste
                'attr' => ['class' => 'form-select'], // Style Bootstrap
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CandidatureSpontanee::class,
        ]);
    }
}
