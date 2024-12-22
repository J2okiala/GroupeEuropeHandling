<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class FiltrerCandidatureSpontaneeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
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
                'placeholder' => 'Sélectionnez un poste',
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Afficher les candidatures',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }
}




                    