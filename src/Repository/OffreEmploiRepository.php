<?php

namespace App\Repository;

use App\Entity\OffreEmploi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OffreEmploi>
 *
 * @method OffreEmploi|null find($id, $lockMode = null, $lockVersion = null)
 * @method OffreEmploi|null findOneBy(array $criteria, array $orderBy = null)
 * @method OffreEmploi[]    findAll()
 * @method OffreEmploi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OffreEmploiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffreEmploi::class);
    }

    /**
     * Ajouter une nouvelle offre d'emploi.
     */
    public function save(OffreEmploi $offreEmploi, bool $flush = false): void
    {
        $this->getEntityManager()->persist($offreEmploi);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprimer une offre d'emploi.
     */
    public function remove(OffreEmploi $offreEmploi, bool $flush = false): void
    {
        $this->getEntityManager()->remove($offreEmploi);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Exemple : Récupérer les offres par type de contrat.
     */
    public function findByTypeContrat(string $typeContrat): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.typeContrat = :typeContrat')
            ->setParameter('typeContrat', $typeContrat)
            ->orderBy('o.datePublication', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Exemple : Récupérer les offres par employeur.
     */
    public function findByEmployeur(int $employeurId): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.employeur = :employeurId')
            ->setParameter('employeurId', $employeurId)
            ->orderBy('o.datePublication', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
