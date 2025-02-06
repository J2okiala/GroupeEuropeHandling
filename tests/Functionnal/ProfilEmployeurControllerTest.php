<?php

namespace App\Tests\Functionnal;

use App\Repository\UtilisateurRepository;
use App\Repository\OffreEmploiRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProfilEmployeurControllerTest extends WebTestCase
{

    private function getEmployeurUser($client)
    {
        $userRepository = $client->getContainer()->get(UtilisateurRepository::class);
        
        // Vérifier si un employeur existe dans la base
        $user = $userRepository->findOneBy(['email' => 'cedric@gmail.com']);

        if (!$user) {
            throw new \Exception('Aucun utilisateur employeur trouvé en base de données.');
        }

        return $user;
    }

    /**
     * Test si la page du profil employeur est accessible après connexion.
     */
    public function testAccesProfilEmployeurAvecConnexion()
    {
        $client = static::createClient();
        $user = $this->getEmployeurUser($client);
    
        $this->assertNotNull($user, "L'utilisateur employeur doit exister.");
    
        $client->loginUser($user);
        $client->request('GET', '/profilEmployeur');
    
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="postez_offre_emploi_form"]');
    }    

    /**
     * Test la redirection si l'utilisateur non authentifié tente d'accéder au profil.
     */
    public function testProfilEmployeurRedirectIfNotAuthenticated()
    {
        $client = static::createClient();
        $client->request('GET', '/profilEmployeur');
        $this->assertResponseRedirects('/connexion');
    }


    /**
     * Test l'affichage de la liste des offres.
     */
    public function testVoirListeOffres()
    {
        $client = static::createClient();
        $user = $this->getEmployeurUser($client);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/mesOffresE');
        $this->assertResponseIsSuccessful();

        // Modifie le test pour vérifier si les cartes des offres sont présentes
        $this->assertSelectorExists('.card');
    }

}
