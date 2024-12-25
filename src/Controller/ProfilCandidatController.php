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
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        // Récuperer l'utilisateur depuis la session
        $utilisateur = $this->getUser();
    
        // Créer le formulaire de recherche
        $formRecherche = $this->createForm(FiltreOffreEmploiFormType::class, null, [
            'method' => 'GET',
        ]);
    
        // Traiter le formulaire
        $formRecherche->handleRequest($request);
    
        // Récupérer les données pour filtrer les offres
        $criteria = [];
        if ($formRecherche->isSubmitted() && $formRecherche->isValid()) {
            $criteria = $formRecherche->getData();
        }
    
        // Récupérer la page actuelle et définir la limite d'offres par page
        $page = $request->query->getInt('page', 1); // Par défaut, page 1
        $limit = 4; // Nombre d'offres par page
    
        // Appliquer la pagination avec les critères
        $offres = $offreEmploiRepository->searchWithPagination($criteria, $paginator, $page, $limit);
    
        // Calculer le nombre total d'offres
        $nombreOffres = $offres->getTotalItemCount();
    
        // Retourner la vue associée
        return $this->render('pages/utilisateur/candidat/profil-candidat.html.twig', [
            'candidatNavbar' => true,
            'formRecherche' => $formRecherche->createView(),
            'offres' => $offres,
            'nombreOffres' => $nombreOffres,
            'currentPage' => $page,
            'totalPages' => ceil($offres->getTotalItemCount() / $limit), // Nombre total de pages
        ]);
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

        // Vérification si le répertoire existe
        if (!$uploadDirectory) {
            $uploadDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads'; // Défini par défaut
        }
    
        // Vérification : l'utilisateur est bien lié au candidat
        if (!$candidat || $candidat->getUtilisateur() !== $utilisateur) {
            $this->addFlash('error', 'Accès refusé ou candidat introuvable.');
            return $this->redirectToRoute('profilCandidat');
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
    
        // Récupérer le candidat lié à l'utilisateur
        $candidat = $candidatRepository->findOneBy(['utilisateur' => $utilisateur]);

        if (!$candidat) {
            $this->addFlash('error', 'Aucun candidat associé à cet utilisateur.');
            return $this->redirectToRoute('app_logout');
        }

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

    #[Route('/postuler/{offreId}', name: 'postuler', methods: ['POST'])]
    public function postuler(
        CandidatRepository $candidatRepository,
        OffreEmploiRepository $offreEmploiRepository,
        EntityManagerInterface $entityManager,
        int $offreId
    ): Response {
        // Récupérer l'utilisateur connecté
        $utilisateur = $this->getUser();

        // Vérifier si l'utilisateur est bien un candidat
        $candidat = $candidatRepository->findOneBy(['utilisateur' => $utilisateur]);

        if (!$candidat) {
            $this->addFlash('error', 'Candidat introuvable.');
            return $this->redirectToRoute('connexion');
        }

        // Récupérer l'offre d'emploi par son ID
        $offreEmploi = $offreEmploiRepository->find($offreId);

        if (!$offreEmploi) {
            $this->addFlash('error', 'Offre d\'emploi introuvable.');
            return $this->redirectToRoute('profilCandidat');
        }

        // Ajouter l'offre à la collection du candidat
        $candidat->addOffresEmploi($offreEmploi);

        // Sauvegarder les changements
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez postulé à l\'offre avec succès.');

        return $this->redirectToRoute('profilCandidat');
    }

}