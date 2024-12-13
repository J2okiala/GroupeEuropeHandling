<?php

namespace App\Controller;

use App\Entity\Candidat;
use App\Form\MesIdentifiantsDeConnexionFormType;
use App\Form\modifierInformationCandidatTypeForm;
use App\Repository\CandidatRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;


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
            'isSecondaryNavbar' => true,
        ]);
    }

    #[Route('/supprimer-compte', name: 'supprimer_compte', methods: ['GET'])]
    public function Suppression(): Response {
        return $this->render('pages/utilisateur/candidat/supprimer-mon-compte.html.twig');
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