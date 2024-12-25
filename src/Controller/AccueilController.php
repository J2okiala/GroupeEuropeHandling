<?php

namespace App\Controller;

use App\Form\FiltreOffreEmploiFormType;
use App\Repository\OffreEmploiRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AccueilController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home()
    {
        return $this->render('pages/home/index.html.twig');

    }

    #[Route('/nosOffres', name: 'nosOffres', methods:['GET', 'POST'])]
    public function nosOffres(Request $request, PaginatorInterface $paginator, OffreEmploiRepository $offreRepository): Response
    {
        // 1. Créer le formulaire de recherche
        $formRecherche = $this->createForm(FiltreOffreEmploiFormType::class);
        $formRecherche->handleRequest($request);
    
        // 2. Récupérer les paramètres de recherche soumis
        $criteria = [];
        if ($formRecherche->isSubmitted() && $formRecherche->isValid()) {
            $criteria = $formRecherche->getData(); // Récupère les données du formulaire
        }
    
        // 3. Utiliser le repository pour appliquer les filtres et paginer
        $page = $request->query->getInt('page', 1); // Récupère la page actuelle ou 1 par défaut
        $limit = 6; // Nombre d'offres par page
        $offres = $offreRepository->searchWithPagination($criteria, $paginator, $page, $limit);
    
        // 4. Calculer le nombre total d'offres pour l'affichage
        $nombreOffres = $offres->getTotalItemCount();
    
        // 5. Renvoyer la réponse avec les paramètres nécessaires
        return $this->render('pages/home/nosOffres.html.twig', [
            'offreNavbar' => true,
            'formRecherche' => $formRecherche->createView(),
            'offres' => $offres,
            'nombreOffres' => $nombreOffres,
            'currentPage' => $page,
            'totalPages' => ceil($offres->getTotalItemCount() / $limit), // Nombre total de pages
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