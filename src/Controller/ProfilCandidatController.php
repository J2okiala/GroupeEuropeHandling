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
        return $this->render('pages/utilisateur/profil-candidat.html.twig');
    }

    #[Route("/deconnexion", name:"deconnexion")]
    public function logout() {
        // peut etre vide
    }


}