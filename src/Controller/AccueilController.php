<?php

namespace App\Controller;

use App\Form\FiltreOffreEmploiFormType;
use App\Repository\OffreEmploiRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\Routing\Annotation\Route;


class AccueilController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home()
    {
        return $this->render('pages/home/index.html.twig');

    }

    #[Route('/nosOffres', name: 'nosOffres')]
    public function nosOffres(OffreEmploiRepository $offreEmploiRepository, HttpFoundationRequest $request)
    {
        // Créer le formulaire de recherche
        $formRecherche = $this->createForm(FiltreOffreEmploiFormType::class, null, [
            'method' => 'GET',
        ]);
    
        // Traiter le formulaire
        $formRecherche->handleRequest($request);
    
        // Initialiser les offres
        $offres = [];
    
        if ($formRecherche->isSubmitted() && $formRecherche->isValid()) {
            // Filtrer les offres en fonction des données du formulaire
            $data = $formRecherche->getData();
            $offres = $offreEmploiRepository->findByFiltre($data); // Méthode personnalisée à créer dans votre repository
        } else {
            // Afficher toutes les offres par défaut
            $offres = $offreEmploiRepository->findAll();
        }
    
        // Calculer le nombre total d'offres
        $nombreOffres = count($offres);
    
        return $this->render('pages/home/nosOffres.html.twig', [
            'offreNavbar' => true,
            'formRecherche' => $formRecherche->createView(),
            'offres' => $offres,
            'nombreOffres' => $nombreOffres, // Passer le nombre d'offres au template
        ]);
    }

    #[Route('/nosServices', name: 'nosServices')]
    public function nosServices()
    {
        return $this->render('pages/home/nosServices.html.twig');

    }

    #[Route('/nosChiffres', name: 'nosChiffres')]
    public function chiffres()
    {
        return $this->render('pages/home/nosChiffres.html.twig');

    }

    #[Route('/mentionsLegales', name: 'mentionsLegales')]
    public function mentions()
    {
        return $this->render('pages/home/mentionsLegales.html.twig');

    }
}