<?php

namespace App\test\Unitaire;

use App\Entity\Candidat;
use App\Entity\Utilisateur;
use PHPUnit\Framework\TestCase;
use Faker\Factory; // Génère des valeurs dynamiques et aléatoires pour chaque test : noms, prénoms, emails, mots de passe, etc.

class CandidatTest extends TestCase
{
    private $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create(); // Initialisation de Faker pour les données aléatoires
    }

    public function testGettersAndSetters()
    {
        $candidat = new Candidat();

        // Génération de données aléatoires
        $nom = $this->faker->lastName();
        $prenom = $this->faker->firstName();
        $nationalite = $this->faker->randomElement(['francais', 'etranger']);
        $dateNaissance = $this->faker->dateTimeBetween('-50 years', '-18 years'); // Date aléatoire pour un adulte
        $telephone = $this->faker->phoneNumber();
        $poste = $this->faker->jobTitle();
        $dateDisponibilite = $this->faker->dateTimeBetween('now', '+1 year');
        $cv = $this->faker->url();
        $lettreMotivation = $this->faker->text(200);
        
        // Création d'un utilisateur factice pour la relation
        $utilisateur = new Utilisateur();
        $utilisateur->setNom($nom)->setPrenom($prenom)->setEmail($this->faker->email());

        // Test du setter et getter du nom
        $candidat->setNom($nom);
        $this->assertEquals($nom, $candidat->getNom());

        // Test du setter et getter du prénom
        $candidat->setPrenom($prenom);
        $this->assertEquals($prenom, $candidat->getPrenom());

        // Test du setter et getter de la nationalité
        $candidat->setNationalite($nationalite);
        $this->assertEquals($nationalite, $candidat->getNationalite());

        // Test du setter et getter de la date de naissance
        $candidat->setDateNaissance($dateNaissance);
        $this->assertEquals($dateNaissance, $candidat->getDateNaissance());

        // Test du setter et getter du téléphone
        $candidat->setTelephone($telephone);
        $this->assertEquals($telephone, $candidat->getTelephone());

        // Test du setter et getter du poste
        $candidat->setPoste($poste);
        $this->assertEquals($poste, $candidat->getPoste());

        // Test du setter et getter de la date de disponibilité
        $candidat->setDateDisponibilite($dateDisponibilite);
        $this->assertEquals($dateDisponibilite, $candidat->getDateDisponibilite());

        // Test du setter et getter du CV
        $candidat->setCv($cv);
        $this->assertEquals($cv, $candidat->getCv());

        // Test du setter et getter de la lettre de motivation
        $candidat->setLettreMotivation($lettreMotivation);
        $this->assertEquals($lettreMotivation, $candidat->getLettreMotivation());

        // Test de l'association avec un utilisateur
        $candidat->setUtilisateur($utilisateur);
        $this->assertEquals($utilisateur, $candidat->getUtilisateur());
    }

    // public function testAddRemoveOffresEmploi()
    // {
    //     $candidat = new Candidat();
    //     $offre1 = $this->createMock(\App\Entity\OffreEmploi::class);
    //     $offre2 = $this->createMock(\App\Entity\OffreEmploi::class);

    //     // Test ajout d'une offre d'emploi
    //     $candidat->addOffresEmploi($offre1);
    //     $this->assertCount(1, $candidat->getOffresEmploi());

    //     // Test ajout d'une seconde offre
    //     $candidat->addOffresEmploi($offre2);
    //     $this->assertCount(2, $candidat->getOffresEmploi());

    //     // Test suppression d'une offre
    //     $candidat->removeOffresEmploi($offre1);
    //     $this->assertCount(1, $candidat->getOffresEmploi());
    // }
}
