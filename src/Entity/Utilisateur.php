<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 10)]
    #[Assert\Choice(['homme', 'femme'])]
    private ?string $civilite = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private ?string $prenom = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Length(min: 8)]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\OneToOne(mappedBy: 'utilisateur', targetEntity: Candidat::class, cascade: ['persist', 'remove'])]
    private ?Candidat $candidat = null;

    #[ORM\OneToOne(mappedBy: 'utilisateur', targetEntity: Employeur::class, cascade: ['persist', 'remove'])]
    private ?Employeur $employeur = null;

    //Getters et Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(string $civilite): self
    {
        $this->civilite = $civilite;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (empty($roles)) {
            $roles[] = 'ROLE_CANDIDAT';
        }
        return array_unique($roles);
    }

    
    public function setRoles(array $roles): self
    {
        if (!is_array($roles)) {
            throw new \InvalidArgumentException('Roles must be an array.');
        }
        $this->roles = array_values(array_unique($roles));
        return $this;
    }

    public function getSingleRole(): ?string
    {
        // Retourne le premier rôle ou null si le tableau est vide
        return $this->roles[0] ?? null;
    }

    public function setSingleRole(string $role): self
    {
        // Stocke un tableau avec un seul rôle
        $this->roles = [$role];
        return $this;
    }


    public function getCandidat(): ?Candidat
    {
        return $this->candidat;
    }

    public function setCandidat(?Candidat $candidat): self
    {
        if ($candidat !== null && $candidat->getUtilisateur() !== $this) {
            $candidat->setUtilisateur($this);
        }
        $this->candidat = $candidat;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Nettoyer les données sensibles si nécessaire
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getEmployeur(): ?Employeur
    {
        return $this->employeur;
    }

    public function setEmployeur(?Employeur $employeur): self
    {
        // Définir la relation bidirectionnelle
        if ($employeur !== null && $employeur->getUtilisateur() !== $this) {
            $employeur->setUtilisateur($this);
        }

        $this->employeur = $employeur;

        return $this;
    }

}
