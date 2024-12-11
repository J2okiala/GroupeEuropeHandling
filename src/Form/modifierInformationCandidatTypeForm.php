<?php

namespace App\Form;

use App\Entity\Candidat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class modifierInformationCandidatTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nationalite', ChoiceType::class, [
                'choices' => [
                    'Français' => 'francais',
                    'Étranger' => 'etranger',
                ],
                'label' => 'Nationalité',
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('dateNaissance', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de naissance',
                'html5' => true,
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
            ])
            ->add('poste', ChoiceType::class, [
                'choices' => [
                    'Agent de piste' => 'Agent de piste',
                    'Agent de escale' => 'Agent de escale',
                    'Agent de trafic' => 'Agent de trafic',
                ],
                'label' => 'Poste',
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('dateDisponibilite', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de disponibilité',
                'html5' => true,
            ])
            ->add('cv', FileType::class, [
                'label' => 'CV (PDF seulement)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier PDF valide.',
                    ]),
                ],
            ])
            ->add('lettreMotivation', FileType::class, [
                'label' => 'Lettre de motivation (PDF seulement)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier PDF valide.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidat::class,
        ]);
    }
}
