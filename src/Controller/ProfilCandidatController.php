<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Form\FiltreOffreEmploiFormType;
use App\Form\MesIdentifiantsDeConnexionFormType;
use App\Form\ModifierInformationCandidatTypeForm;
use App\Repository\CandidatRepository;
use App\Repository\OffreEmploiRepository;
use App\Repository\UtilisateurRepository;
use App\Service\CandidatService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfilCandidatController extends AbstractController
{
    #[Route("/profilCandidat", name: "profilCandidat", methods: ['GET'])]
    #[IsGranted('ROLE_CANDIDAT')]
    public function profil(
        OffreEmploiRepository $offreEmploiRepository,
        CandidatRepository $candidatRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        // Récupérer l'utilisateur connecté
        $utilisateur = $this->getUser();
    
        // Vérification si l'utilisateur est connecté
        if (!$utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('connexion');
        }

        // Récupérer le candidat lié à cet utilisateur
        $candidat = $candidatRepository->findOneBy(['utilisateur' => $utilisateur]);

        // Vérification : si aucun candidat n'est trouvé
        if (!$candidat) {
            $this->addFlash('error', 'Aucun candidat associé à cet utilisateur.');
            return $this->redirectToRoute('profilCandidat');
        }
    
        // Récupération des offres déjà postulées par l'utilisateur
        $offresPostulees = $candidatRepository->findOffresPostuleesByUser($utilisateur->getId());
    
        // Création du formulaire de recherche pour filtrer les offres d'emploi
        $formRecherche = $this->createForm(FiltreOffreEmploiFormType::class, null, [
            'method' => 'GET', // Utilisation de la méthode GET pour la recherche
        ]);
        $formRecherche->handleRequest($request);
    
        // Déclaration des variables
        $offres = [];
        $nombreOffres = 0;
        $page = $request->query->getInt('page', 1); // Page courante, par défaut 1
        $limit = 4; // Limite d'affichage par page
    
        // Si le formulaire est soumis et valide, on filtre les résultats selon les critères
        if ($formRecherche->isSubmitted() && $formRecherche->isValid()) {
            $criteria = $formRecherche->getData();
            // Recherche avec pagination en fonction des critères
            $offres = $offreEmploiRepository->searchWithPagination($criteria, $paginator, $page, $limit);
            $nombreOffres = $offres->getTotalItemCount(); // Nombre total d'offres correspondant aux critères
        } else {
            // Sinon, on affiche toutes les offres avec pagination
            $offres = $offreEmploiRepository->searchWithPagination([], $paginator, $page, $limit);
            $nombreOffres = $offres->getTotalItemCount(); // Nombre total d'offres sans filtre
        }
    
        // Calcul du nombre total de pages pour la pagination
        $totalPages = ceil($offres->getTotalItemCount() / $limit);
    
        // Rendu du template avec toutes les variables nécessaires
        return $this->render('pages/utilisateur/candidat/profil-candidat.html.twig', [
            'candidatNavbar' => true,
            'formRecherche' => $formRecherche->createView(),
            'offres' => $offres,
            'nombreOffres' => $nombreOffres,
            'offresPostulees' => array_map(fn($offre) => $offre['id'], $offresPostulees), // Transformation en tableau d'IDs
            'currentPage' => $page, // Page courante
            'totalPages' => $totalPages, // Nombre total de pages pour la pagination
        ]);
    }

    #[Route('/postuler/{offreId}', name: 'postuler', methods: ['POST'])]
    #[IsGranted('ROLE_CANDIDAT')]
    public function postuler(
        CandidatRepository $candidatRepository,
        OffreEmploiRepository $offreEmploiRepository,
        EntityManagerInterface $entityManager,
        int $offreId
    ): Response {
        $utilisateur = $this->getUser();
        $candidat = $candidatRepository->findOneBy(['utilisateur' => $utilisateur]);

        if (!$candidat) {
            $this->addFlash('error', 'Candidat introuvable.');
            return $this->redirectToRoute('connexion');
        }

        $offreEmploi = $offreEmploiRepository->find($offreId);

        if (!$offreEmploi) {
            $this->addFlash('error', 'Offre d\'emploi introuvable.');
            return $this->redirectToRoute('profilCandidat');
        }

        $candidat->addOffresEmploi($offreEmploi);
        $entityManager->persist($candidat);
        $entityManager->flush();


        $this->addFlash('success', 'Vous avez postulé à l\'offre avec succès.');

        return $this->redirectToRoute('profilCandidat');
    }

    #[Route("/deconnexion", name:"deconnexion")]
    #[IsGranted('ROLE_CANDIDAT')]
    public function logout() {
        // peut etre vide
    }

    #[Route('/maFiche', name: 'maFiche')]
    #[IsGranted('ROLE_CANDIDAT')]
    public function maFiche( 
        Request $request, 
        CandidatRepository $candidatRepository
        ): Response{
        // Récupérer l'utilisateur connecté
        $utilisateur = $this->getUser();
    
        // Vérification si l'utilisateur est connecté
        if (!$utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('connexion');
        }

        // Récupérer le candidat lié à cet utilisateur
        $candidat = $candidatRepository->findOneBy(['utilisateur' => $utilisateur]);

        // Vérification : si aucun candidat n'est trouvé
        if (!$candidat) {
            $this->addFlash('error', 'Aucun candidat associé à cet utilisateur.');
            return $this->redirectToRoute('profilCandidat');
        }

        return $this->render('pages/utilisateur/candidat/ma-fiche.html.twig', [
            'candidatNavbar' => true,
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formRecherche' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
        ]);
    }

    #[Route('/profil-candidat/modifier/{id}', name: 'modifierMesInformations')]
    #[IsGranted('ROLE_CANDIDAT')]
    public function modifierMesInformations(
        Request $request,
        Candidat $candidat,
        CandidatService $candidatService // Injection du service
    ): Response {
        // Récupérer l'utilisateur connecté
        $utilisateur = $this->getUser();

        // Vérification: si aucun candidat n'est trouvé
        if ($candidat->getUtilisateur() !== $utilisateur) {
            $this->addFlash('error', 'Accès refusé.');
            return $this->redirectToRoute('profilCandidat');
        }
    
        // Création du formulaire
        $form = $this->createForm(ModifierInformationCandidatTypeForm::class, $candidat);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $candidatService->updateCandidat(
                    $candidat,
                    $form->get('cv')->getData(),
                    $form->get('lettreMotivation')->getData()
                );
                $this->addFlash('success', 'Profil mis à jour avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
    
            return $this->redirectToRoute('profilCandidat');
        }
    
        return $this->render('pages/utilisateur/candidat/modifier-mes-informations.html.twig', [
            'form' => $form->createView(),
            'candidatNavbar' => true,
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formRecherche' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
        ]);
    }


    #[Route('/mesCandidatures', name: 'mesCandidatures')]
    #[IsGranted('ROLE_CANDIDAT')]
    public function mesCandidatures(CandidatRepository $candidatRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $utilisateur = $this->getUser();
    
        // Récupérer les offres postulées via le repository
        $offresPostulees = $candidatRepository->findOffresPostuleesByUser($utilisateur->getId());
    
        if (empty($offresPostulees)) {
            $this->addFlash('error', 'Aucune candidature trouvée.');
            return $this->redirectToRoute('connexion');
        }
    
        return $this->render('pages/utilisateur/candidat/mes-candidatures.html.twig', [
            'candidatNavbar' => true,
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formRecherche' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
            'offresPostulees' => $offresPostulees,
        ]);
    }


    #[Route('/mesIdentifiantsDeConnexion', name: 'mesIdentifiantsDeConnexion', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CANDIDAT')]
    public function mesIdentifiantsDeConnexion(
        Request $request,
        CandidatRepository $candidatRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $utilisateur = $this->getUser();
    
        // Récupérer le candidat lié à l'utilisateur
        $candidat = $candidatRepository->findOneBy(['utilisateur' => $utilisateur]);

        // Créer le formulaire pour l'entité Candidat
        $form = $this->createForm(MesIdentifiantsDeConnexionFormType::class, $candidat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $plainPassword = $form->get('password')->getData();
        
            // Mettre à jour l'entité Utilisateur
            $utilisateur->setEmail($email);
            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($utilisateur, $plainPassword);
                $utilisateur->setPassword($hashedPassword);
            }
        
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            $this->addFlash('success', 'Vos identifiants ont été mis à jour avec succès.');
            return $this->redirectToRoute('profilCandidat');
        }
    
        return $this->render('pages/utilisateur/candidat/mes-identifiants-de-connexion.html.twig', [
            'form' => $form->createView(),
            'candidatNavbar' => true,
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formRecherche' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
        ]);
    }

    #[Route('/supprimer-compte', name: 'supprimer_compte', methods: ['GET'])]
    #[IsGranted('ROLE_CANDIDAT')]
    public function pageSuppression(): Response {
        return $this->render('pages/utilisateur/candidat/supprimer-mon-compte.html.twig', [
            'candidatNavbar' => true,
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formRecherche' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
        ]);
    }
    
    #[Route('/confirmer-compte', name: 'confirmer_compte', methods: ['POST'])]
    #[IsGranted('ROLE_CANDIDAT')]
    public function supprimerCompte(
        Request $request,
        SessionInterface $session, // Injection de la session
        UtilisateurRepository $utilisateurRepository,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        // Vérification CSRF
        $csrfToken = $request->request->get('_csrf_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete_account', $csrfToken))) {
            $this->addFlash('error', 'Action non autorisée.');
            return $this->redirectToRoute('profilCandidat');
        }
    
        $utilisateur = $this->getUser();
        if (!$utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour supprimer votre compte.');
            return $this->redirectToRoute('connexion');
        }
    
        // Suppression de l'utilisateur
        $utilisateurRepository->remove($utilisateur, true);
    
        // Invalidation de la session
        $session->invalidate();
    
        // Déconnexion de l'utilisateur
        $this->container->get('security.token_storage')->setToken(null);
    
        $this->addFlash('success', 'Votre compte a été supprimé avec succès.');
        return $this->redirectToRoute('home');
    }

}