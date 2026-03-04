<?php

namespace App\Repository;

use App\Entity\Candidat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class CandidatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Candidat::class);
    }

    /**
     * Ajouter un nouvel candidat dans la base de données.
     * @param Candidat $entity
     * @param bool $flush
     * 
     * @return void
     */
    public function save(Candidat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprimer un candidat de la base de données.
     * @param Candidat $entity
     * @param bool $flush
     * 
     * @return void
     */
    public function remove(Candidat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Exemple de méthode personnalisée : trouver les candidats disponibles après une date
     * @param \DateTimeInterface $date
     * 
     * @return array
     */
    public function findAvailableAfter(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.dateDisponibilite >= :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve l'offre par l'utilisateur qui a postuler
     * @param int $userId
     * 
     * @return array
     */
    public function findOffresPostuleesByUser(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.offresEmploi', 'o')
            ->where('c.utilisateur = :userId')
            ->setParameter('userId', $userId)
            ->select('o.id, o.poste, o.typeContrat, o.localisation, o.datePublication')
            ->getQuery()
            ->getResult();
    }


}