<?php

namespace App\Repository;

use App\Entity\Employeur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Employeur>
 *
 * @method Employeur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employeur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employeur[]    findAll()
 * @method Employeur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
    public function add(Employeur $employeur): void
    {
        $this->_em->persist($employeur);
        $this->_em->flush();
    }

    /**
     * Supprimer un employeur de la base de données.
     *
     * @param Employeur $employeur
     */
    public function remove(Employeur $employeur): void
    {
        $this->_em->remove($employeur);
        $this->_em->flush();
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
