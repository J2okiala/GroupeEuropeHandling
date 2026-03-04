<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use App\Document\CandidatureSpontanee;
use App\Entity\Candidat;
use App\Entity\Employeur;
use App\Entity\OffreEmploi;
use App\Entity\Utilisateur;
use App\Form\FiltrerCandidatureSpontaneeFormType;
use App\Form\MesIdentifiantsDeConnexionEFormType;
use App\Form\ModifierInformationEmployeurTypeForm;
use App\Form\PostezOffreEmploiFormType;
use App\Repository\EmployeurRepository;
use App\Repository\OffreEmploiRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\CandidatureSpontaneeRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
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
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfilEmployeurController extends AbstractController
{

    #[Route("/profilEmployeur", name:"profilEmployeur")]
    #[IsGranted('ROLE_EMPLOYEUR')]
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
            return $this->redirectToRoute('profilEmployeur');
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

    #[Route("/deconnexion", name:"deconnexion")]
    #[IsGranted('ROLE_EMPLOYEUR')]
    public function logout() {
        // peut etre vide
    }

    #[Route('/maFicheE', name: 'maFicheE')]
    #[IsGranted('ROLE_EMPLOYEUR')]
    public function maFiche() {
        // Récuperer l'utilisateur depuis la session
        $utilisateur = $this->getUser();
        // Vérification : l'utilisateur est bien lié à l'employeur
        if (!$utilisateur) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('profilEmployeur');
        }

        return $this->render('pages/utilisateur/employeur/ma-fiche.html.twig', [
        'employeurNavbar' => true,
        'withFiltrer' => false, // Pas de filtrage sur cette page
        'formPoster' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
        ]);
    }

    #[Route('/profil-employeur/modifier/{id}', name: 'modifierMesInformationsE')]
    #[IsGranted('ROLE_EMPLOYEUR')]
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

    #[Route('/mesOffresE', name:'mesOffresE')]
    #[IsGranted('ROLE_EMPLOYEUR')]
    public function mesOffres(
        OffreEmploiRepository $offreEmploiRepository, 
        EmployeurRepository $employeurRepository,
    ): Response {
        // Récupérer l'utilisateur connecté
        $utilisateur = $this->getUser();
        
        // Récupérer l'employeur associé à l'utilisateur connecté
        $employeur = $employeurRepository->findOneBy(['utilisateur' => $utilisateur]);

        // Vérification : l'utilisateur est bien lié à un employeur
        if (!$employeur) {
            $this->addFlash('error', 'Accès refusé ou employeur introuvable.');
            return $this->redirectToRoute('profilEmployeur');
        }

        // Récupérer les offres de l'employeur
        $offres = $offreEmploiRepository->findBy(['employeur' => $employeur]);

        // Récupérer les candidats ayant postulé à ces offres
        $candidatures = [];
        foreach ($offres as $offre) {
            // Les candidats qui ont postulé à cette offre (via la relation ManyToMany)
            $candidats = $offre->getCandidats();

            $candidatures[] = [
                'offre' => $offre,
                'candidats' => $candidats
            ];
        }

        // Rendu de la page avec les offres et candidatures
        return $this->render('pages/utilisateur/employeur/mes-offres.html.twig', [
            'employeurNavbar' => true,
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formPoster' => null, // Si vous n'avez pas besoin de ce formulaire
            'offres' => $offres,
            'candidatures' => $candidatures,
        ]);
    }

    #[Route('/telecharger-cv/{candidatId}', name:'telecharger_cv')]
    #[IsGranted('ROLE_EMPLOYEUR')]
    public function telechargerCv(int $candidatId, ManagerRegistry $doctrine): Response
    {
        // Utiliser le ManagerRegistry pour accéder au repository de l'entité Candidat
        $candidat = $doctrine->getRepository(Candidat::class)->find($candidatId);

        // Vérifier si le candidat existe et s'il a un CV
        if (!$candidat || !$candidat->getCv()) {
            throw $this->createNotFoundException('Candidat ou CV non trouvé.');
        }

        // Récupérer le chemin complet du fichier CV
        $cvPath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $candidat->getCv();

        // Vérifier si le fichier existe
        if (!file_exists($cvPath)) {
            throw new FileNotFoundException('Le fichier CV est introuvable.');
        }

        // Créer une réponse de téléchargement du fichier
        return new StreamedResponse(function () use ($cvPath) {
            readfile($cvPath);
        }, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf', // Ou un autre type MIME selon ton fichier
            'Content-Disposition' => 'attachment; filename="' . basename($cvPath) . '"',
        ]);
    }

    #[Route('/telecharger-lettre-motivation/{candidatId}', name:'telecharger_lettre_motivation')]
    #[IsGranted('ROLE_EMPLOYEUR')]
    public function telechargerLettreMotivation(int $candidatId, ManagerRegistry $doctrine): Response
    {
        // Utiliser le ManagerRegistry pour accéder au repository de l'entité Candidat
        $candidat = $doctrine->getRepository(Candidat::class)->find($candidatId);
    
        // Vérifier si le candidat existe et s'il a une lettre de motivation
        if (!$candidat || !$candidat->getLettreMotivation()) {
            throw $this->createNotFoundException('Candidat ou lettre de motivation non trouvée.');
        }
    
        // Récupérer le chemin complet du fichier lettre de motivation
        $lettreMotivationPath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $candidat->getLettreMotivation();
    
        // Vérifier si le fichier existe
        if (!file_exists($lettreMotivationPath)) {
            throw new FileNotFoundException('Le fichier de la lettre de motivation est introuvable.');
        }
    
        // Créer une réponse de téléchargement du fichier
        return new StreamedResponse(function () use ($lettreMotivationPath) {
            readfile($lettreMotivationPath);
        }, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf', // Ou un autre type MIME si nécessaire
            'Content-Disposition' => 'attachment; filename="' . basename($lettreMotivationPath) . '"',
        ]);
    } 

    #[Route('/supprimer-offre/{id}', name: 'supprimer_offre', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYEUR')]
    public function SuppressionOffre(
        int $id,
        OffreEmploiRepository $offreEmploiRepository
    ): Response {
        // Trouve l'offre d'emploi par son ID
        $offreEmploi = $offreEmploiRepository->find($id);
    
        if (!$offreEmploi) {
            // Affiche une erreur si l'offre n'existe pas
            $this->addFlash('danger', "L'offre d'emploi n'existe pas.");
            return $this->redirectToRoute('liste_offres'); // Redirige vers une page listant les offres
        }
    
        // Supprime l'offre
        $offreEmploiRepository->remove($offreEmploi, true); // true pour effectuer le flush
    
        // Ajoute un message de succès
        $this->addFlash('success', "L'offre d'emploi a été supprimée avec succès.");
    
        // Redirige vers la liste des offres après suppression
        return $this->redirectToRoute('mesOffresE');
    }

    #[Route('/filtrer-candidatures', name: 'filtrer_candidatures', methods: ['GET'])]
    #[IsGranted('ROLE_EMPLOYEUR')]
    public function afficherCandidaturesFiltrees(
        Request $request, 
        CandidatureSpontaneeRepository $candidatureSpontaneeRepository, 
        LoggerInterface $logger
    ): Response {

        $form = $this->createForm(FiltrerCandidatureSpontaneeFormType::class);
        $form->handleRequest($request);


        $candidatures = [];
        $toutesCandidatures = $candidatureSpontaneeRepository->findAll(); // Récupère toutes les candidatures
    
        if ($form->isSubmitted() && $form->isValid()) {
            $poste = $form->get('poste')->getData();
    
            if (!empty($poste)) {
                dump('Poste filtré : ' . $poste); // Vérifie la valeur de $poste
                $candidatures = $candidatureSpontaneeRepository->findByPoste($poste);
                dump($candidatures); // Vérifie si la requête retourne quelque chose
            }
            
        }
        $logger->info('Request data: ' . json_encode($candidatures));
        return $this->render('pages/utilisateur/employeur/afficher-les-candidatures-spontanee.html.twig', [
            'form' => $form->createView(),
            'candidatures' => $candidatures,
            'toutesCandidatures' => $toutesCandidatures, // Passer toutes les candidatures pour le cas "else"
            'employeurNavbar' => true,
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formPoster' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
        ]);
    }

    
    #[Route('/mesIdentifiantsDeConnexionE', name: 'mesIdentifiantsDeConnexionE', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_EMPLOYEUR')]
    public function mesIdentifiantsDeConnexionE(
        Request $request,
        EmployeurRepository $employeurRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        
        $utilisateur = $this->getUser();

        // Récupérer l'employeur lié à l'utilisateur
        $employeur = $employeurRepository->findOneBy(['utilisateur' => $utilisateur]);
        // dd($employeur);
        
        // Vérification : l'utilisateur est bien lié à un employeur
        if (!$employeur) {
            $this->addFlash('error', 'Accès refusé ou employeur introuvable.');
            return $this->redirectToRoute('profilEmployeur');
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
            'withFiltrer' => false,
            'formPoster' => null,
        ]);
    }

    #[Route('/supprimer-compteE', name: 'supprimer-compteE', methods: ['GET'])]
    #[IsGranted('ROLE_EMPLOYEUR')]
    public function Suppression(): Response {
        return $this->render('pages/utilisateur/employeur/supprimer-mon-compte.html.twig', [
            'employeurNavbar' => true,
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formPoster' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
        ]);
    }

    #[Route('/confirmer_suppression-compteE', name: 'confirmer_suppression-compteE', methods: ['POST'])]
    #[IsGranted('ROLE_EMPLOYEUR')]
    public function supprimerCompteE(
        Request $request,
        SessionInterface $session,
        UtilisateurRepository $utilisateurRepository,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        // Vérification CSRF
        $csrfToken = $request->request->get('_csrf_token');
        if (!$csrfToken || !$csrfTokenManager->isTokenValid(new CsrfToken('delete_account', $csrfToken))) {
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

        // Déconnexion et invalidation de la session
        $session->invalidate();
        $this->container->get('security.token_storage')->setToken(null);

        $this->addFlash('success', 'Votre compte a été supprimé avec succès.');
        return $this->redirectToRoute('home');
    }

}


