<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\Candidat;
use App\Entity\Employeur;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateurCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Utilisateur) {
            return;
        }

        // Hachage du mot de passe
        if ($entityInstance->getPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword());
            $entityInstance->setPassword($hashedPassword);
        }

        // Vérification du rôle et création des entités associées (Employeur, Candidat ou Admin)
        $roles = $entityInstance->getRoles();

        if (in_array('ROLE_EMPLOYEUR', $roles)) {
            $employeur = new Employeur();
            $employeur->setUtilisateur($entityInstance);
            $employeur->setNom($entityInstance->getNom());
            $employeur->setPrenom($entityInstance->getPrenom());

            $entityManager->persist($employeur);
        }

        if (in_array('ROLE_ADMIN', $roles)) {
            $admin = new Admin();
            $admin->setUtilisateur($entityInstance);
            $admin->setNom($entityInstance->getNom());
            $admin->setPrenom($entityInstance->getPrenom());

            $entityManager->persist($admin);
        }

        if (in_array('ROLE_CANDIDAT', $roles)) {
            $candidat = new Candidat();
            $candidat->setUtilisateur($entityInstance);
            $candidat->setNom($entityInstance->getNom());
            $candidat->setPrenom($entityInstance->getPrenom());

            $entityManager->persist($candidat);
        }

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public static function getEntityFqcn(): string
    {
        return Utilisateur::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            ChoiceField::new('civilite')
                ->setChoices([
                    'homme' => 'homme',
                    'femme' => 'femme',
                ])
                ->allowMultipleChoices(false) // Un seul rôle possible
                ->renderExpanded(false) // Affiche une liste déroulante
                ->setRequired(true) // Rendre le champ obligatoire
                ->hideWhenUpdating(),
            TextField::new('nom')->hideWhenUpdating(),
            TextField::new('prenom')->hideWhenUpdating(),
            EmailField::new('email')->hideWhenUpdating(),
            TextField::new('password')->hideWhenUpdating()->hideOnIndex(),
            ChoiceField::new('singleRole')
                ->setChoices([
                    'Candidat' => 'ROLE_CANDIDAT',
                    'Employeur' => 'ROLE_EMPLOYEUR',
                    'Admin' => 'ROLE_ADMIN',
                ])
                ->allowMultipleChoices(false) // Un seul rôle possible
                ->renderExpanded(false) // Affiche une liste déroulante
                ->setRequired(true) // Rendre le champ obligatoire
                ->hideWhenUpdating(),
        ];
    }
}
