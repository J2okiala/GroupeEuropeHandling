<?php

namespace App\tests\Unitaire;

use App\Entity\Employeur;
use App\Entity\Utilisateur;
use PHPUnit\Framework\TestCase;
use Faker\Factory; // Génère des valeurs dynamiques et aléatoires pour chaque test : noms, prénoms, emails, mots de passe, etc.

class EmployeurTest extends TestCase
{
    private $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create(); // Initialisation de Faker
    }

    public function testGettersAndSetters()
    {
        $employeur = new Employeur();

        // Génération de données aléatoires
        $nom = $this->faker->lastName();
        $prenom = $this->faker->firstName();
        $entreprise = $this->faker->company();
        
        // Création d'un utilisateur factice pour la relation
        $utilisateur = new Utilisateur();
        $utilisateur->setNom($nom)
                    ->setPrenom($prenom)
                    ->setEmail($this->faker->email());

        // Test du setter et getter du nom
        $employeur->setNom($nom);
        $this->assertEquals($nom, $employeur->getNom());

        // Test du setter et getter du prénom
        $employeur->setPrenom($prenom);
        $this->assertEquals($prenom, $employeur->getPrenom());

        // Test du setter et getter de l'entreprise
        $employeur->setEntreprise($entreprise);
        $this->assertEquals($entreprise, $employeur->getEntreprise());

        // Test de l'association avec un utilisateur
        $employeur->setUtilisateur($utilisateur);
        $this->assertEquals($utilisateur, $employeur->getUtilisateur());
    }

    // public function testAddRemoveOffres()
    // {
    //     $employeur = new Employeur();
    //     $offre1 = $this->createMock(\App\Entity\OffreEmploi::class);
    //     $offre2 = $this->createMock(\App\Entity\OffreEmploi::class);

    //     // Test ajout d'une offre d'emploi
    //     $employeur->addOffre($offre1);
    //     $this->assertCount(1, $employeur->getOffres());

    //     // Test ajout d'une seconde offre
    //     $employeur->addOffre($offre2);
    //     $this->assertCount(2, $employeur->getOffres());

    //     // Test suppression d'une offre
    //     $employeur->removeOffre($offre1);
    //     $this->assertCount(1, $employeur->getOffres());
    // }
}
