<?php

namespace App\Repository;

use App\Entity\Admin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Admin::class);
    }

    /**
     * Ajouter un nouvel admin dans la base de données.
     *
     * @param Admin $admin
     */
    public function save(Admin $admin): void
    {
        $entityManager = $this->getEntityManager(); // Utilisez cette méthode pour obtenir l'EntityManager
        $entityManager->persist($admin);
        $entityManager->flush();
    }

    /**
     * Supprimer un admin de la base de données.
     *
     * @param Admin $employeur
     */
    public function remove(Admin $admin): void
    {
        $entityManager = $this->getEntityManager(); // Utilisez cette méthode pour obtenir l'EntityManager
        $entityManager->remove($admin);
        $entityManager->flush();
    }


}
