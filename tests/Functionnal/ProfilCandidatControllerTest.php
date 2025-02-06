<?php

namespace App\Tests\Functionnal;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProfilCandidatControllerTest extends WebTestCase
{
    private function getCandidatUser($client)
    {
        $userRepository = $client->getContainer()->get(UtilisateurRepository::class);
        return $userRepository->findOneByEmail('jojo@gmail.com'); // Assure-toi que cet utilisateur existe en BDD
    }

    /**
     * Test si la page du profil candidat est accessible après connexion.
     */
    public function testProfilCandidatPageAccessible()
    {
        $client = static::createClient();
        $user = $this->getCandidatUser($client);

        // Connexion de l'utilisateur
        $client->loginUser($user);
        
        // Accéder à la page de profil
        $client->request('GET', '/profilCandidat');
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test la redirection si l'utilisateur non authentifié tente d'accéder au profil.
     */
    public function testProfilCandidatRedirectIfNotAuthenticated()
    {
        $client = static::createClient();
        $client->request('GET', '/profilCandidat');
        $this->assertResponseRedirects('/connexion');
    }

    /**
     * Test si un candidat peut postuler à une offre.
     */
    public function testPostulerToOffre()
    {
        $client = static::createClient();
        $user = $this->getCandidatUser($client);
        $client->loginUser($user);

        $client->request('POST', '/postuler/1'); // Assure-toi que l'ID 1 existe en BDD
        $this->assertResponseRedirects('/profilCandidat');

        $client->followRedirect();
        $this->assertSelectorExists('.alert-success');
    }

    /**
     * Test si la déconnexion fonctionne correctement.
     */
    public function testDeconnexion()
    {
        $client = static::createClient();
        $user = $this->getCandidatUser($client);
        $client->loginUser($user);

        $client->request('GET', '/deconnexion');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/');
    }

    /**
     * Test la modification des informations du candidat.
     */
    public function modifierMesInformations(?string $uploadDirectory)
    {
        $client = static::createClient();
        $user = $this->getCandidatUser($client);
        $client->loginUser($user);
    
        // Vérifier que l'utilisateur a un ID valide
        $this->assertNotNull($user->getId(), "L'utilisateur doit avoir un ID");
    
        // Accéder à la page de modification
        $crawler = $client->request('GET', '/profil-candidat/modifier/' . $user->getId());
        $this->assertResponseIsSuccessful();
    
        // Vérifier si le bouton "Enregistrer" existe
        $this->assertGreaterThan(0, $crawler->filter('button:contains("Enregistrer")')->count(), 'Le bouton Enregistrer est introuvable');
    
        // Récupérer et soumettre le formulaire
        $form = $crawler->selectButton('Mettre à jour')->form([
            'modifier_information_candidat_type[nom]' => 'NouveauNom',
        ]);        
        $client->submit($form);
    
        $this->assertResponseRedirects('/profilCandidat');
        $client->followRedirect();
        $this->assertSelectorExists('.alert-success');
    }
    
}
