<?php

namespace App\Entity;

use App\Repository\OffreEmploiRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OffreEmploiRepository::class)]
class OffreEmploi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le poste est obligatoire.")]
    private ?string $poste = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le type de contrat est obligatoire.")]
    private ?string $typeContrat = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "La description du poste est obligatoire.")]
    private ?string $descriptionPoste = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "La modalité de travail est obligatoire.")]
    private ?string $modaliteTravail = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "La localisation est obligatoire.")]
    private ?string $localisation = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $datePublication;

    #[ORM\ManyToOne(targetEntity: Employeur::class, inversedBy: 'offres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employeur $employeur = null;

    public function __construct()
    {
        $this->datePublication = new \DateTime();
    }

    // Getters et setters

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTypeContrat(): ?string
    {
        return $this->typeContrat;
    }

    public function setTypeContrat(string $typeContrat): self
    {
        $this->typeContrat = $typeContrat;

        return $this;
    }

    public function getDescriptionPoste(): ?string
    {
        return $this->descriptionPoste;
    }

    public function setDescriptionPoste(string $descriptionPoste): self
    {
        $this->descriptionPoste = $descriptionPoste;

        return $this;
    }

    public function getModaliteTravail(): ?string
    {
        return $this->modaliteTravail;
    }

    public function setModaliteTravail(string $modaliteTravail): self
    {
        $this->modaliteTravail = $modaliteTravail;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getDatePublication(): \DateTimeInterface
    {
        return $this->datePublication;
    }

    public function getEmployeur(): ?Employeur
    {
        return $this->employeur;
    }

    public function setEmployeur(?Employeur $employeur): self
    {
        $this->employeur = $employeur;

        return $this;
    }
}
