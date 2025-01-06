<?php

namespace App\Repository;

use App\Entity\OffreEmploi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

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


    /**
     * Filtrer les offres d'emploi en fonction des critères fournis, avec pagination.
     */
    public function findByFiltre(array $criteria): array
    {
        $qb = $this->createQueryBuilder('o');

        // Filtrer par poste
        if (!empty($criteria['poste'])) {
            $qb->andWhere('o.poste LIKE :poste')
            ->setParameter('poste', '%' . $criteria['poste'] . '%');
        }

        // Filtrer par type de contrat
        if (!empty($criteria['typeContrat'])) {
            $qb->andWhere('o.typeContrat = :typeContrat')
            ->setParameter('typeContrat', $criteria['typeContrat']);
        }

        // Filtrer par modalité de travail
        if (!empty($criteria['modaliteTravail'])) {
            $qb->andWhere('o.modaliteTravail = :modaliteTravail')
            ->setParameter('modaliteTravail', $criteria['modaliteTravail']);
        }

        // Filtrer par localisation
        if (!empty($criteria['localisation'])) {
            $qb->andWhere('o.localisation LIKE :localisation')
            ->setParameter('localisation', '%' . $criteria['localisation'] . '%');
        }

        // Trier par date de publication (décroissant)
        $qb->orderBy('o.datePublication', 'DESC');

        // Gestion de la pagination
        if (isset($criteria['limit'])) {
            $qb->setMaxResults($criteria['limit']);
        }

        if (isset($criteria['offset'])) {
            $qb->setFirstResult($criteria['offset']);
        }

        // Retourner les résultats
        return $qb->getQuery()->getResult();
        }

    public function searchByCriteria(array $criteria)
    {
        $qb = $this->createQueryBuilder('o'); // 'o' est un alias pour OffreEmploi

        if (!empty($criteria['poste'])) {
            $qb->andWhere('o.poste LIKE :poste')
                ->setParameter('poste', '%' . $criteria['poste'] . '%');
        }

        if (!empty($criteria['typeContrat'])) {
            $qb->andWhere('o.typeContrat = :typeContrat')
                ->setParameter('typeContrat', $criteria['typeContrat']);
        }

        if (!empty($criteria['localisation'])) {
            $qb->andWhere('o.localisation LIKE :localisation')
                ->setParameter('localisation', '%' . $criteria['localisation'] . '%');
        }

        return $qb->getQuery(); // Retourne une Query pour la pagination
    }

    public function searchWithPagination(array $criteria, PaginatorInterface $paginator, int $page, int $limit)
    {
        $qb = $this->createQueryBuilder('o');
    
        if (!empty($criteria['poste'])) {
            $qb->andWhere('o.poste LIKE :poste')
                ->setParameter('poste', '%' . $criteria['poste'] . '%');
        }
    
        if (!empty($criteria['typeContrat'])) {
            $qb->andWhere('o.typeContrat = :typeContrat')
                ->setParameter('typeContrat', $criteria['typeContrat']);
        }
    
        if (!empty($criteria['modaliteTravail'])) {
            $qb->andWhere('o.modaliteTravail = :modaliteTravail')
                ->setParameter('modaliteTravail', $criteria['modaliteTravail']);
        }
    
        if (!empty($criteria['localisation'])) {
            $qb->andWhere('o.localisation LIKE :localisation')
                ->setParameter('localisation', '%' . $criteria['localisation'] . '%');
        }
    
        $qb->orderBy('o.datePublication', 'DESC');
    
        // Utilisation de KnpPaginator
        return $paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit
        );
    }
    


}
