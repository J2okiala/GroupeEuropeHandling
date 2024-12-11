<?php

namespace App\Controller;

use App\Form\ProfilFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfilCandidatController extends AbstractController
{
    #[Route("/profilCandidat", name:"profilCandidat")]
    public function profil() {
        // Récuperer l'utilisateur depuis la session
        $uilisateur = $this->getUser();

        // Faire ce que vous vous voulez avec, comme récuperer des donnés ect..

        // Retourner la vue associé
        return $this->render('pages/utilisateur/candidat/profil-candidat.html.twig', ['isSecondaryNavbar' => true]);
    }

    #[Route("/deconnexion", name:"deconnexion")]
    public function logout() {
        // peut etre vide
    }

    #[Route('/maFiche', name: 'maFiche')]
    public function maFiche()
    {
        // Récuperer l'utilisateur depuis la session
        $uilisateur = $this->getUser();

        return $this->render('pages/utilisateur/candidat/ma-fiche.html.twig', [
            'isSecondaryNavbar' => true,
        ]);
    }


    #[Route('/modifierMesInformations', name: 'modifierMesInformations')]
    public function modifierMesInformations()
    {
        // Récuperer l'utilisateur depuis la session
        $uilisateur = $this->getUser();

        return $this->render('pages/utilisateur/candidat/modifier-mes-informations.html.twig', [
            'isSecondaryNavbar' => true,
        ]);
    }

    #[Route('/mesCandidatures', name: 'mesCandidatures')]
    public function mesCandidatures()
    {
        // Récuperer l'utilisateur depuis la session
        $uilisateur = $this->getUser();

        return $this->render('pages/utilisateur/candidat/mes-candidatures.html.twig', [
            'isSecondaryNavbar' => true,
        ]);
    }

    #[Route('/mesAlertes', name: 'mesAlertes')]
    public function mesAlertes()
    {
        // Récuperer l'utilisateur depuis la session
        $uilisateur = $this->getUser();

        return $this->render('pages/utilisateur/candidat/mes-alertes.html.twig', [
            'isSecondaryNavbar' => true,
        ]);
    }

    #[Route('/modifierMesPiecesJointes', name: 'modifierMesPiecesJointes')]
    public function modifierMesPiecesJointes()
    {
        // Récuperer l'utilisateur depuis la session
        $uilisateur = $this->getUser();

        return $this->render('pages/utilisateur/candidat/modifier-mes-pieces-jointes.html.twig', [
            'isSecondaryNavbar' => true,
        ]);
    }

    #[Route('/mesIdentifiantsDeConnexion', name: 'mesIdentifiantsDeConnexion')]
    public function mesIdentifiantsDeConnexion()
    {
        // Récuperer l'utilisateur depuis la session
        $uilisateur = $this->getUser();

        return $this->render('pages/utilisateur/candidat/mes-identifiants-de-connexion.html.twig', [
            'isSecondaryNavbar' => true,
        ]);
    }

    #[Route('/desactiverMonCompte', name: 'desactiverMonCompte')]
    public function desactiverMonCompte()
    {
        // Récuperer l'utilisateur depuis la session
        $uilisateur = $this->getUser();

        return $this->render('pages/utilisateur/candidat/desactiver-mon-compte.html.twig', [
            'isSecondaryNavbar' => true,
        ]);
    }



}