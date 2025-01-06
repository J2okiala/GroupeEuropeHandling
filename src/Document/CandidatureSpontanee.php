<?php

namespace App\Document;

use App\Repository\CandidatureSpontaneeRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(repositoryClass: CandidatureSpontaneeRepository::class)] 
class CandidatureSpontanee 
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: 'string')]
    private $cv;

    #[MongoDB\Field(type: 'string')]
    private $lm;

    #[MongoDB\Field(type: 'string')]
    private $poste;

    #[MongoDB\Field(type: 'date')] // Nouveau champ pour la date de candidature
    private $date;

    // Getters et Setters

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(string $cv): self
    {
        $this->cv = $cv;
        return $this;
    }

    public function getLm(): ?string
    {
        return $this->lm;
    }

    public function setLm(string $lm): self
    {
        $this->lm = $lm;
        return $this;
    }

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(string $poste): self
    {
        $this->poste = $poste;
        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }
}
