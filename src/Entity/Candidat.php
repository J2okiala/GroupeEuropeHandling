<?php

namespace App\Entity;

use App\Repository\CandidatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CandidatRepository::class)]
class Candidat 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Assert\Choice(['francais', 'etranger'])]
    private ?string $nationalite = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Assert\NotBlank]
    private ?string $telephone = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Assert\NotBlank]
    private ?string $poste = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $dateDisponibilite = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $cv = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $lettreMotivation = null;

    #[ORM\OneToOne(inversedBy: 'candidat', targetEntity: Utilisateur::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $prenom = null;

    #[ORM\ManyToMany(targetEntity: OffreEmploi::class, inversedBy: 'candidats')]
    #[ORM\JoinTable(name: 'candidats_offres_emploi')]
    private Collection $offresEmploi;

    // Constructeur

    public function __construct()
    {
        $this->offresEmploi = new ArrayCollection();
    }


    //Getter et Setter

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(string $nationalite): self
    {
        $this->nationalite = $nationalite;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;
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

    public function getDateDisponibilite(): ?\DateTimeInterface
    {
        return $this->dateDisponibilite;
    }

    public function setDateDisponibilite(\DateTimeInterface $dateDisponibilite): self
    {
        $this->dateDisponibilite = $dateDisponibilite;
        return $this;
    }

    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(?string $cv): self
    {
        $this->cv = $cv;
        return $this;
    }

    public function getLettreMotivation(): ?string
    {
        return $this->lettreMotivation;
    }

    public function setLettreMotivation(?string $lettreMotivation): self
    {
        $this->lettreMotivation = $lettreMotivation;
        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    // Getter et setter pour la relation
    public function getOffresEmploi(): Collection
    {
        return $this->offresEmploi;
    }

    public function addOffresEmploi(OffreEmploi $offreEmploi): self
    {
        if (!$this->offresEmploi->contains($offreEmploi)) {
            $this->offresEmploi[] = $offreEmploi;
        }

        return $this;
    }

    public function removeOffresEmploi(OffreEmploi $offreEmploi): self
    {
        $this->offresEmploi->removeElement($offreEmploi);

        return $this;
    }

}
