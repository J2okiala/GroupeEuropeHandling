<?php

namespace App\tests\Functionnal;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UtilisateurControllerTest extends WebTestCase
{
    /**
     * Test si la page d'inscription est accessible.
     */
    public function testInscriptionPage()
    {
        $client = static::createClient();
        $client->request('GET', '/inscription');
        
        $this->assertResponseIsSuccessful();
        // Vérifier que le formulaire d'inscription est présent
        $this->assertSelectorExists('form[name="inscription_form"]');
    }

    /**
     * Test si l'inscription fonctionne correctement avec des données valides.
     */
    public function testInscriptionSubmitValid()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');

        // Générer une adresse e-mail unique
        $uniqueEmail = 'testuser_' . uniqid() . '@example.com';

        $form = $crawler->selectButton('Inscription')->form([
            'inscription_form[civilite]' => '1',
            'inscription_form[nom]' => 'Test',
            'inscription_form[prenom]' => 'User',
            'inscription_form[email]' => $uniqueEmail, // Utilisation de l'e-mail unique
            'inscription_form[password]' => 'password123',
            'inscription_form[confirmPassword]' => 'password123',
        ]);

        $client->submit($form);
        
        // Vérifier que l'utilisateur est redirigé après l'inscription
        $this->assertResponseRedirects('/nosOffres');
    }

    /**
     * Test si la page de connexion est accessible.
     */
    public function testConnexionPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/connexion');
        
        $this->assertResponseIsSuccessful();
        // Vérifier que le formulaire de connexion est bien présent
        $this->assertSelectorExists('form[name="connexion_form"]');
    }
    
    /**
     * Test si la connexion fonctionne correctement avec des identifiants valides.
     */
    public function testConnexionSubmitValid()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/connexion');
    
        // Vérifier que le formulaire est bien présent
        $this->assertSelectorExists('form[name="connexion_form"]');
    
        // Sélectionner le formulaire avec les bons champs
        $form = $crawler->selectButton('Connexion')->form([
            'email' => 'jojo@gmail.com',  // Correspond au name du champ email
            'password' => 'jojo1234',     // Correspond au name du champ password
        ]);
    
        $client->submit($form);
    
        // Vérifier la redirection après connexion réussie
        $this->assertResponseRedirects('/profilCandidat');
        $client->followRedirect();
    }
    
    /**
     * Test la connexion avec des mails incorrects.
     */
    public function testConnexionSubmitInvalid()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/connexion');
    
        // Vérifier que le formulaire est bien présent
        $this->assertSelectorExists('form[name="connexion_form"]');
    
        // Soumettre le formulaire avec des identifiants incorrects
        $form = $crawler->selectButton('Connexion')->form([
            'email' => 'notanemail@example.com',  // Faux email
            'password' => 'WrongPassword!',       // Mauvais mot de passe
        ]);
    
        $client->submit($form);
    
        // Vérifier que la réponse redirige vers /connexion
        $this->assertResponseRedirects('/connexion');
    
        // Suivre la redirection
        $crawler = $client->followRedirect();
    
        // 🔍 Afficher le vrai message d'erreur pour le vérifier
        $errorMessage = $crawler->filter('.alert-danger')->text();
        dump($errorMessage); // Permet de voir le message exact
    
        // Adapter le test au message affiché réellement
        $this->assertSelectorExists('.alert-danger');
        $this->assertSelectorTextContains('.alert-danger', 'Invalid credentials.');
    }

}
