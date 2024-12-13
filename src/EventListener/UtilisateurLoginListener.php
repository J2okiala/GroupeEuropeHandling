<?php

namespace App\EventListener;

use App\Entity\Utilisateur;
use App\Entity\Employeur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class UtilisateurLoginListener
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function onLogin(AuthenticationEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof Utilisateur && in_array('ROLE_EMPLOYEUR', $user->getRoles(), true) && !$user->getEmployeur()) {
            $employeur = new Employeur();
            $employeur->setUtilisateur($user);
            $employeur->setNomEntreprise('Nom par défaut');

            $this->entityManager->persist($employeur);
            $this->entityManager->flush();
        }
    }
}
