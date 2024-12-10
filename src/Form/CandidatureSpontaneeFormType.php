<?php

namespace App\Form;

use App\Document\CandidatureSpontanee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidatureSpontaneeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cv', FileType::class, [
                'label' => 'Votre CV (PDF)',
                'mapped' => false, // pour indiquer qu'il ne s'agit pas d'un champ mappé directement à l'entité
                'required' => true,
            ])
            ->add('lm', FileType::class, [
                'label' => 'Votre Lettre de Motivation (PDF)',
                'mapped' => false,
                'required' => true,
            ])
            ->add('poste', ChoiceType::class, [
                'label' => 'Poste souhaité',
                'choices' => [
                    'Agent de piste' => 'agent_de_piste',
                    'Agent d\'escale' => 'agent_d_escale',
                    'Pilote' => 'pilote',
                    'Hôtesse de l\'air / Steward' => 'hotesse_steward',
                ],
                'placeholder' => 'Sélectionnez un poste',
                'attr' => ['class' => 'form-select'], // Bootstrap style
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CandidatureSpontanee::class,
        ]);
    }
}
