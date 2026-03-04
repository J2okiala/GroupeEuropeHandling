<?php

namespace App\test\Unitaire;

use App\Entity\Utilisateur;
use PHPUnit\Framework\TestCase;
use Faker\Factory; // Génère des valeurs dynamiques et aléatoires pour chaque test : noms, prénoms, emails, mots de passe, etc.

class UtilisateurTest extends TestCase
{
    private $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create(); // Initialise Faker pour générer des données aléatoires
    }

    public function testGettersAndSetters()
    {
        $utilisateur = new Utilisateur();

        $nom = $this->faker->lastName();
        $prenom = $this->faker->firstName();
        $email = $this->faker->email();
        $password = $this->faker->password(8); // Mot de passe aléatoire de 8 caractères
        $civilite = $this->faker->randomElement(['homme', 'femme']); // Choix aléatoire entre "homme" et "femme"

        // Test du setter et getter du nom
        $utilisateur->setNom($nom);
        $this->assertEquals($nom, $utilisateur->getNom());

        // Test du setter et getter du prénom
        $utilisateur->setPrenom($prenom);
        $this->assertEquals($prenom, $utilisateur->getPrenom());

        // Test du setter et getter de l'email
        $utilisateur->setEmail($email);
        $this->assertEquals($email, $utilisateur->getEmail());

        // Test du setter et getter du mot de passe
        $utilisateur->setPassword($password);
        $this->assertEquals($password, $utilisateur->getPassword());

        // Test du setter et getter de la civilité
        $utilisateur->setCivilite($civilite);
        $this->assertEquals($civilite, $utilisateur->getCivilite());
    }

    public function testRoles()
    {
        $utilisateur = new Utilisateur();

        // Par défaut, l'utilisateur doit avoir le rôle ROLE_CANDIDAT
        $this->assertContains("ROLE_CANDIDAT", $utilisateur->getRoles());

        // Génération d'un rôle aléatoire parmi les rôles possibles
        $roles = ["ROLE_ADMIN", "ROLE_USER", "ROLE_EMPLOYEUR"];
        $randomRole = $this->faker->randomElement($roles);

        // Ajouter un rôle et vérifier
        $utilisateur->setRoles([$randomRole]);
        $this->assertContains($randomRole, $utilisateur->getRoles());

        // Vérifier la méthode getSingleRole()
        $utilisateur->setSingleRole($randomRole);
        $this->assertEquals($randomRole, $utilisateur->getSingleRole());
    }

    public function testEraseCredentials()
    {
        $utilisateur = new Utilisateur();
        
        // La méthode eraseCredentials() ne doit rien faire ici,
        // on vérifie juste qu'elle s'exécute sans erreur
        $utilisateur->eraseCredentials();
        $this->assertTrue(true);
    }

    public function testGetUserIdentifier()
    {
        $utilisateur = new Utilisateur();
        $email = $this->faker->email(); // Email aléatoire

        $utilisateur->setEmail($email);

        // Vérifie que l'identifiant utilisateur est bien l'email
        $this->assertEquals($email, $utilisateur->getUserIdentifier());
    }
}
