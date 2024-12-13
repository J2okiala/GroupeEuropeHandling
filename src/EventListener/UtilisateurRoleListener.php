<?php

namespace App\EventListener;

use App\Entity\Utilisateur;
use App\Entity\Employeur;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs as EventLifecycleEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;

class UtilisateurRoleListener
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function preUpdate(EventLifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Utilisateur) {
            return;
        }

        // Vérifiez si le rôle ROLE_EMPLOYEUR est attribué ET si l'entité Employeur n'existe pas encore
        if (in_array('ROLE_EMPLOYEUR', $entity->getRoles(), true) && !$entity->getEmployeur()) {
            // Créez une nouvelle entité Employeur
            $employeur = new Employeur();
            $employeur->setUtilisateur($entity);
            $employeur->setNomEntreprise('Nom par défaut'); // Remplacez avec une valeur réelle ou laissez vide

            // Persistez et flushez immédiatement
            $this->entityManager->persist($employeur);
            $this->entityManager->flush(); // Cette opération persiste immédiatement l'entité
        }
    }
}
