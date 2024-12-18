<?php

namespace App\Controller;

use App\Entity\Employeur;
use App\Entity\OffreEmploi;
use App\Form\MesIdentifiantsDeConnexionEFormType;
use App\Form\ModifierInformationEmployeurTypeForm;
use App\Form\OffreEmploiFormType;
use App\Form\PostezOffreEmploiFormType;
use App\Form\ProfilFormType;
use App\Repository\EmployeurRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfilEmployeurController extends AbstractController
{
    #[Route("/profilEmployeur", name:"profilEmployeur")]
    public function profile(
        Request $request,
        EmployeurRepository $employeurRepository // Injectez le repository
    ): Response {
        // Récupérer l'utilisateur connecté
        $utilisateur = $this->getUser();
    
        // Vérifier si l'utilisateur est connecté
        if (!$utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('connexion');
        }
    
        // Récupérer l'employeur lié à cet utilisateur
        $employeur = $employeurRepository->findOneBy(['utilisateur' => $utilisateur]);
    
        // Vérification : si aucun employeur n'est trouvé
        if (!$employeur) {
            $this->addFlash('error', 'Aucun employeur associé à cet utilisateur.');
            return $this->redirectToRoute('connexion');
        }
    
        // Créer une nouvelle offre d'emploi
        $offre = new OffreEmploi();
        $offre->setEmployeur($employeur);
    
        // Créer et gérer le formulaire
        $formPoster = $this->createForm(PostezOffreEmploiFormType::class, $offre);
        return $this->render('pages/utilisateur/employeur/profil-employeur.html.twig', [
            'employeurNavbar' => true,
            'formPoster' => $formPoster->createView(),
        ]);
    }

    #[Route("/deconnexion", name:"deconnexion")]
    public function logout() {
        // peut etre vide
    }



    #[Route('/poster-offre-emploi', name: 'poster-offre-emploi', methods: ['POST'])]
    public function traiterFormulaire(
        Request $request,
        EntityManagerInterface $entityManager,
        EmployeurRepository $employeurRepository // Injectez le repository
    ): Response {
        // Récupérer l'utilisateur connecté
        $utilisateur = $this->getUser();

        // Vérifier si l'utilisateur est connecté
        if (!$utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('connexion');
        }

        // Récupérer l'employeur lié à cet utilisateur
        $employeur = $employeurRepository->findOneBy(['utilisateur' => $utilisateur]);

        // Vérification : si aucun employeur n'est trouvé
        if (!$employeur) {
            $this->addFlash('error', 'Aucun employeur associé à cet utilisateur.');
            return $this->redirectToRoute('profilEmployeur');
        }

        // Créer une nouvelle offre d'emploi
        $offre = new OffreEmploi();
        $offre->setEmployeur($employeur);

        // Créer et gérer le formulaire
        $form = $this->createForm(PostezOffreEmploiFormType::class, $offre);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder l'offre d'emploi dans la base de données
            $entityManager->persist($offre);
            $entityManager->flush();

            // Afficher un message de succès et rediriger vers la page de profil employeur
            $this->addFlash('success', 'Offre d\'emploi publiée avec succès !');
            return $this->redirectToRoute('profilEmployeur');
        }

        // Si le formulaire n'est pas valide, on redirige vers la page d'affichage du formulaire
        return $this->redirectToRoute('profilEmployeur');
    }



    #[Route('/maFicheE', name: 'maFicheE')]
    public function maFiche()
    {
        // Récuperer l'utilisateur depuis la session
        $uilisateur = $this->getUser();

        return $this->render('pages/utilisateur/employeur/ma-fiche.html.twig', [
        'employeurNavbar' => true,
        'withFiltrer' => false, // Pas de filtrage sur cette page
        'formPoster' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
        ]);
    }

    #[Route('/profil-employeur/modifier/{id}', name: 'modifierMesInformationsE')]
    public function modifierMesInformations(
        Request $request,
        Employeur $employeur,
        EmployeurRepository $employeurRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer l'utilisateur connecté
        $utilisateur = $this->getUser();
    
        // Vérification : l'utilisateur est bien lié à l'employeur
        if (!$employeur || $employeur->getUtilisateur() !== $utilisateur) {
            $this->addFlash('error', 'Accès refusé ou employeur introuvable.');
            return $this->redirectToRoute('profilEmployeur');
        }
    
        // Créer le formulaire
        $form = $this->createForm(ModifierInformationEmployeurTypeForm::class, $employeur);
        $form->handleRequest($request);
    
        // Vérification de la soumission et de la validité du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // Synchroniser les informations avec l'utilisateur
            $utilisateur->setNom($employeur->getNom());
            $utilisateur->setPrenom($employeur->getPrenom());
    
            // Sauvegarder les modifications
            $entityManager->flush(); // Persister les changements
    
            $this->addFlash('success', 'Informations mises à jour avec succès !');
            return $this->redirectToRoute('profilEmployeur');
        }
    
        return $this->render('pages/utilisateur/employeur/modifier-mes-informations.html.twig', [
            'form' => $form->createView(),
            'employeurNavbar' => true,
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formPoster' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
        ]);
    }
    

    #[Route('/mesOffresE', name: 'mesOffresE')]
    public function mesOffres()
    {
        // Récuperer l'utilisateur depuis la session
        // $utilisateur = $this->getUser();

        return $this->render('pages/utilisateur/employeur/mes-offres.html.twig', [
            'employeurNavbar' => true,
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formPoster' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
        ]);
    }

    #[Route('/mesIdentifiantsDeConnexionE', name: 'mesIdentifiantsDeConnexionE', methods: ['GET', 'POST'])]
    public function mesIdentifiantsDeConnexionE(
        Request $request,
        EmployeurRepository $employeurRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $utilisateur = $this->getUser();
    
        // Récupérer le candidat lié à l'utilisateur
        $employeur = $employeurRepository->findOneBy(['utilisateur' => $utilisateur]);
    
        // Récupérer le candidat lié à l'utilisateur
        $employeur = $employeurRepository->findOneBy(['utilisateur' => $utilisateur]);

        if (!$employeur) {
            $this->addFlash('error', 'Aucun employeur associé à cet utilisateur.');
            return $this->redirectToRoute('connexion');
        }

        // Créer le formulaire pour l'entité Employeur
        $form = $this->createForm(MesIdentifiantsDeConnexionEFormType::class, $employeur);
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
            return $this->redirectToRoute('profilEmployeur');
        }
    
        return $this->render('pages/utilisateur/employeur/mes-identifiants-de-connexion.html.twig', [
            'form' => $form->createView(),
            'employeurNavbar' => true,
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formPoster' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
        ]);
    }

    #[Route('/supprimer-compteE', name: 'supprimer_compteE')]
    public function Suppression(): Response {
        return $this->render('pages/utilisateur/employeur/supprimer-mon-compte.html.twig', [
            'employeurNavbar' => true,
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formPoster' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
        ]);
    }
    
    #[Route('/confirmer_suppression-compteE', name: 'confirmer_suppression-compteE', methods: ['POST'])]
    public function supprimerCompteE(
        Request $request,
        SessionInterface $session, // Injection de la session
        UtilisateurRepository $utilisateurRepository,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        // Vérification CSRF
        $csrfToken = $request->request->get('_csrf_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete_account', $csrfToken))) {
            $this->addFlash('error', 'Action non autorisée.');
            return $this->redirectToRoute('profilEmployeur');
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

    #[Route('/candidature-spontanee', name: 'candidature-spontanee', methods: ['GET'])]
    public function CandidatureSpontanee(): Response {
            return $this->render('pages/utilisateur/employeur/les-candidatures-spontanee.html.twig', 
            ['employeurNavbar' => true,
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formPoster' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
        ]);
    }


}