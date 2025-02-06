<?php

namespace App\Tests\Functionnal;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccueilControllerTest extends WebTestCase
{
    /**
     * Test si la page Home est functionnel.
     */
    public function testHomePage()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Groupe Europe Handling'); // Change selon le contenu de ta page
    }

    /**
     * Test si la page NosOffres est functionnel.
     */
    public function testNosOffresPage()
    {
        $client = static::createClient();
        $client->request('GET', '/nosOffres');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form'); // Vérifie si le formulaire de recherche est bien présent
    }

    /**
     * Test si la page NosServices est functionnel.
     */
    public function testNosServicesPage()
    {
        $client = static::createClient();
        $client->request('GET', '/nosServices');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'NOS SERVICES'); // Change selon le contenu de ta page
    }

    /**
     * Test si la page NosChiffres est functionnel.
     */
    public function testNosChiffresPage()
    {
        $client = static::createClient();
        $client->request('GET', '/nosChiffres');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'CHIFFRE CLES'); // Change selon le contenu de ta page
    }

    /**
     * Test si la page MentionsLegales est functionnel.
     */
    public function testMentionsLegalesPage()
    {
        $client = static::createClient();
        $client->request('GET', '/mentionsLegales');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'MENTIONS LÉGALES'); // Change selon le contenu de ta page
    }

    /**
     * Test si la page PolitiquesUtilisation est functionnel.
     */
    public function testPolitiquesUtilisationPage()
    {
        $client = static::createClient();
        $client->request('GET', '/politiquesUtilisation');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "Politiques D'utilisation"); // Change selon le contenu de ta page
    }
}
