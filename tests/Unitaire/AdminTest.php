<?php

namespace App\tests\Unitaire;

use App\Entity\Admin;
use App\Entity\Utilisateur;
use PHPUnit\Framework\TestCase;
use Faker\Factory; // Génère des valeurs dynamiques et aléatoires pour chaque test : noms, prénoms, emails, mots de passe, etc.

class AdminTest extends TestCase
{
    private $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create(); // Initialisation de Faker
    }

    public function testGettersAndSetters()
    {
        $admin = new Admin();

        // Génération de données aléatoires
        $nom = $this->faker->lastName();
        $prenom = $this->faker->firstName();

        // Création d'un utilisateur factice pour la relation
        $utilisateur = new Utilisateur();
        $utilisateur->setNom($nom)
                    ->setPrenom($prenom)
                    ->setEmail($this->faker->email());

        // Test du setter et getter du nom
        $admin->setNom($nom);
        $this->assertEquals($nom, $admin->getNom());

        // Test du setter et getter du prénom
        $admin->setPrenom($prenom);
        $this->assertEquals($prenom, $admin->getPrenom());

        // Test de l'association avec un utilisateur
        $admin->setUtilisateur($utilisateur);
        $this->assertEquals($utilisateur, $admin->getUtilisateur());
    }
}
