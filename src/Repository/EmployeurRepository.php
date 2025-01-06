<?php

namespace App\Repository;

use App\Entity\Employeur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EmployeurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employeur::class);
    }

    /**
     * Ajouter un nouvel employeur dans la base de données.
     *
     * @param Employeur $employeur
     */
    public function save(Employeur $employeur): void
    {
        $entityManager = $this->getEntityManager(); // Utilisez cette méthode pour obtenir l'EntityManager
        $entityManager->persist($employeur);
        $entityManager->flush();
    }

    /**
     * Supprimer un employeur de la base de données.
     *
     * @param Employeur $employeur
     */
    public function remove(Employeur $employeur): void
    {
        $entityManager = $this->getEntityManager(); // Utilisez cette méthode pour obtenir l'EntityManager
        $entityManager->remove($employeur);
        $entityManager->flush();
    }

    /**
     * Exemple de méthode personnalisée pour trouver un employeur par le nom de l'entreprise.
     *
     * @param string $nomEntreprise
     * @return Employeur|null
     */
    public function findByNomEntreprise(string $nomEntreprise): ?Employeur
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.nomEntreprise = :nomEntreprise')
            ->setParameter('nomEntreprise', $nomEntreprise)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
