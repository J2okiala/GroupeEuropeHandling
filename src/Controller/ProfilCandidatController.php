<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Form\FiltreOffreEmploiFormType;
use App\Form\MesIdentifiantsDeConnexionFormType;
use App\Form\ModifierInformationCandidatTypeForm;
use App\Repository\CandidatRepository;
use App\Repository\OffreEmploiRepository;
use App\Repository\UtilisateurRepository;
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


class ProfilCandidatController extends AbstractController
{
    #[Route("/profilCandidat", name: "profilCandidat", methods: ['GET'])]
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
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez postulé à l\'offre avec succès.');

        return $this->redirectToRoute('profilCandidat');
    }


    #[Route("/deconnexion", name:"deconnexion")]
    public function logout() {
        // peut etre vide
    }

    #[Route('/maFiche', name: 'maFiche')]
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
    public function modifierMesInformations(
        Request $request,
        Candidat $candidat,
        CandidatRepository $candidatRepository,
        EntityManagerInterface $entityManager,
        string $uploadDirectory = null // Assurez-vous de configurer ce paramètre
    ): Response {
        // Récupérer l'utilisateur connecté
        $utilisateur = $this->getUser();
    
        // Récupérer le candidat lié à cet utilisateur
        $candidat = $candidatRepository->findOneBy(['utilisateur' => $utilisateur]);

        // Vérification : si aucun candidat n'est trouvé
        if (!$candidat) {
            $this->addFlash('error', 'Aucun candidat associé à cet utilisateur.');
            return $this->redirectToRoute('profilCandidat');
        }

        // Vérification si le répertoire existe
        if (!$uploadDirectory) {
            $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads'; // Défini par défaut
        }
        
        // Créer le formulaire
        $form = $this->createForm(ModifierInformationCandidatTypeForm::class, $candidat);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement des fichiers (CV et lettre de motivation)
            $cvFile = $form->get('cv')->getData();
            $lettreMotivationFile = $form->get('lettreMotivation')->getData();
    
            if ($cvFile) {
                // Gérer l'upload du fichier CV
                $cvFilename = uniqid().'.'.$cvFile->guessExtension(); // Nom unique du fichier
                try {
                    $cvFile->move($uploadDirectory, $cvFilename); // Déplacer le fichier dans le répertoire de stockage
                    $candidat->setCv($cvFilename); // Mettre à jour le champ 'cv' avec le nom du fichier
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload du CV.');
                }
            }
    
            if ($lettreMotivationFile) {
                // Gérer l'upload de la lettre de motivation
                $lettreMotivationFilename = uniqid().'.'.$lettreMotivationFile->guessExtension(); // Nom unique du fichier
                try {
                    $lettreMotivationFile->move($uploadDirectory, $lettreMotivationFilename); // Déplacer le fichier
                    $candidat->setLettreMotivation($lettreMotivationFilename); // Mettre à jour le champ 'lettreMotivation' avec le nom du fichier
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de la lettre de motivation.');
                }
            }
    
            // Synchroniser les changements sur l'utilisateur
            $utilisateur = $candidat->getUtilisateur(); // Récupérer l'utilisateur lié
            $utilisateur->setNom($candidat->getNom());
            $utilisateur->setPrenom($candidat->getPrenom());
    
            // Sauvegarder les modifications
            $entityManager->flush(); // Persister les changements
    
            $this->addFlash('success', 'Informations mises à jour avec succès !');
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
    public function mesCandidatures(CandidatRepository $candidatRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $utilisateur = $this->getUser();

        // Récupérer le candidat associé à l'utilisateur
        $candidat = $candidatRepository->findOneBy(['utilisateur' => $utilisateur]);

        if (!$candidat) {
            $this->addFlash('error', 'Candidat introuvable.');
            return $this->redirectToRoute('connexion');
        }

        // Récupérer toutes les offres auxquelles le candidat a postulé
        $offresPostulees = $candidat->getOffresEmploi();

        return $this->render('pages/utilisateur/candidat/mes-candidatures.html.twig', [
            'candidatNavbar' => true,
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formRecherche' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
            'offresPostulees' => $offresPostulees,
        ]);
    }

    #[Route('/mesIdentifiantsDeConnexion', name: 'mesIdentifiantsDeConnexion', methods: ['GET', 'POST'])]
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
    public function pageSuppression(): Response {
        return $this->render('pages/utilisateur/candidat/supprimer-mon-compte.html.twig', [
            'candidatNavbar' => true,
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formRecherche' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
        ]);
    }
    
    #[Route('/confirmer-compte', name: 'confirmer_compte', methods: ['POST'])]
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