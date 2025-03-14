<?php

namespace App\Repository;

use App\Document\CandidatureSpontanee;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Doctrine\Persistence\Mapping\ClassMetadata;

class CandidatureSpontaneeRepository extends DocumentRepository
{
    public function __construct(DocumentManager $dm)
    {
        parent::__construct(
            $dm,
            $dm->getUnitOfWork(),
            $dm->getClassMetadata('App\Document\CandidatureSpontanee')
        );
    }

    /**
     * Ajoutez une candidatureSpontanee
     * @param CandidatureSpontanee $candidature
     * 
     * @return void
     */
    public function save(CandidatureSpontanee $candidature): void
    {
        $this->dm->persist($candidature);
        $this->dm->flush();
    }


    /**
     * filtre les candidatures spontanee par le poste
     * @param string $poste
     * 
     * @return [type]
     */
    public function findByPoste(string $poste)
    {
        return $this->createQueryBuilder()
            ->field('poste')->equals($poste) // Filtre par le champ "poste"
            ->getQuery()
            ->execute();
    }

}


