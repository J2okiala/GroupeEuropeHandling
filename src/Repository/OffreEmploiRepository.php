<?php

namespace App\Repository;

use App\Entity\OffreEmploi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

class OffreEmploiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffreEmploi::class);
    }

    /**
     * Ajouter une nouvelle offre d'emploi.
     * @param OffreEmploi $offreEmploi
     * @param bool $flush
     * 
     * @return void
     */
    public function save(OffreEmploi $offreEmploi, bool $flush = false): void
    {
        $this->getEntityManager()->persist($offreEmploi);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprimer une offre d'emploi
     * @param OffreEmploi $offreEmploi
     * @param bool $flush
     * 
     * @return void
     */
    public function remove(OffreEmploi $offreEmploi, bool $flush = false): void
    {
        $this->getEntityManager()->remove($offreEmploi);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Récupérer les offres par type de contrat.
     * @param string $typeContrat
     * 
     * @return array
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
     * @param int $employeurId
     * 
     * @return array
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
     * @param array $criteria
     * 
     * @return array
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

    /**
     * permet d'effectuer une recherche avancée sur une entité en fonction de critères dynamiques, 
     * tout en appliquant une pagination pour limiter le nombre de résultats affichés par page.
     * @param array $criteria
     * @param PaginatorInterface $paginator
     * @param int $page
     * @param int $limit
     * 
     * @return [type]
     */
    public function searchWithPagination(array $criteria, PaginatorInterface $paginator, int $page, int $limit)
    {
        // Création d'un QueryBuilder pour récupérer des objets 'o' de l'entité concernée
        $qb = $this->createQueryBuilder('o');
    
        // Ajout d'un filtre sur le poste si un critère est fourni
        if (!empty($criteria['poste'])) {
            $qb->andWhere('o.poste LIKE :poste')
                ->setParameter('poste', '%' . $criteria['poste'] . '%');
        }
    
        // Ajout d'un filtre sur le type de contrat si spécifié
        if (!empty($criteria['typeContrat'])) {
            $qb->andWhere('o.typeContrat = :typeContrat')
                ->setParameter('typeContrat', $criteria['typeContrat']);
        }
    
        // Ajout d'un filtre sur la modalité de travail si fournie
        if (!empty($criteria['modaliteTravail'])) {
            $qb->andWhere('o.modaliteTravail = :modaliteTravail')
                ->setParameter('modaliteTravail', $criteria['modaliteTravail']);
        }
    
        // Ajout d'un filtre sur la localisation si spécifiée
        if (!empty($criteria['localisation'])) {
            $qb->andWhere('o.localisation LIKE :localisation')
                ->setParameter('localisation', '%' . $criteria['localisation'] . '%');
        }
    
        // Tri des résultats par date de publication en ordre décroissant
        $qb->orderBy('o.datePublication', 'DESC');
    
        // Utilisation de KnpPaginator pour paginer les résultats
        return $paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit
        );
    }
    


}
