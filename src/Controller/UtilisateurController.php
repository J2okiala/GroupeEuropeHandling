<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Candidat;
use Symfony\Component\Mailer\MailerInterface; // Import du MailerService
use Symfony\Component\Mime\Email;
use App\Form\ConnexionFormType;
use App\Form\InscriptionFormType;
use App\Repository\UtilisateurRepository;
use App\Repository\CandidatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UtilisateurController extends AbstractController
{
    #[Route('/inscription', name: 'inscription')]
    public function inscription(
        Request $request, 
        UtilisateurRepository $utilisateurRepository, 
        CandidatRepository $candidatRepository, 
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer // Injection du service Mailer
    ): Response {
        // Redirige l'utilisateur vers la page de profil s'il est déjà connecté
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('profilCandidat');
        }
    
        $utilisateur = new Utilisateur();
        $form = $this->createForm(InscriptionFormType::class, $utilisateur);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifie si l'utilisateur existe déjà avec cet email
            $existingUser = $utilisateurRepository->findOneBy(['email' => $utilisateur->getEmail()]);
            if ($existingUser) {
                // Ajoute un message d'erreur si l'email est déjà utilisé
                $this->addFlash('error', 'Un compte existe déjà avec cet email.');
            } else {
                // Hash le mot de passe
                $hashedPassword = $passwordHasher->hashPassword($utilisateur, $utilisateur->getPassword());
                $utilisateur->setPassword($hashedPassword);
    
                // Définit le rôle par défaut : ROLE_CANDIDAT
                $utilisateur->setRoles(['ROLE_CANDIDAT']);
    
                // Crée une entité Candidat associée
                $candidat = new Candidat();
                $candidat->setUtilisateur($utilisateur);
                $candidat->setNom($utilisateur->getNom());
                $candidat->setPrenom($utilisateur->getPrenom());
                $candidatRepository->save($candidat, true);
    
                // Sauvegarde l'utilisateur dans la base de données
                $utilisateurRepository->save($utilisateur, true);
    
                // Génère un lien de confirmation d'inscription
                $confirmationLink = $this->generateUrl('profilCandidat', [], UrlGeneratorInterface::ABSOLUTE_URL);
    
                // Prépare et envoie l'email de confirmation
                $email = (new Email())
                    ->from('noreply@geh.com') // Adresse valide et autorisée par votre serveur
                    ->to($utilisateur->getEmail())
                    ->subject('Confirmation de votre inscription')
                    ->html("
                        <h3>Confirmation de votre inscription</h3>
                        <p>Bonjour " . htmlspecialchars($utilisateur->getPrenom()) . ",</p>
                        <p>Merci pour votre inscription ! Cliquez sur le lien ci-dessous pour confirmer votre inscription :</p>
                        <a href='" . htmlspecialchars($confirmationLink) . "'>Confirmer mon inscription</a>
                    ");
    
                try {
                    $mailer->send($email);
                    $this->addFlash('success', 'Inscription réussie ! Un email de confirmation vous a été envoyé.');
                } catch (\Exception $e) {
                    $this->addFlash('error', "Inscription réussie, mais l'email de confirmation n'a pas pu être envoyé.");
                }
    
                // Redirige après l'inscription
                return $this->redirectToRoute('nosOffres');
            }
        }
    
        return $this->render('pages/utilisateur/inscription.html.twig', [
            'offreNavbar' => true,
            'registrationForm' => $form->createView(),
            'withFiltrer' => false,
            'formRecherche' => null,
        ]);
    }

    #[Route("/connexion", name: "connexion")]
    public function connexion(
        Request $req, 
        AuthenticationUtils $authenticationUtils
    ): Response {
        // Redirige l'utilisateur vers la page de profil s'il est connecté
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('profilCandidat');
        }

        // Récupère les erreurs et le dernier email
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastEmail = $authenticationUtils->getLastUsername() ?? ''; // Assigne une chaîne vide si NULL

        // Création du formulaire de connexion sans entité liée
        $form = $this->createForm(ConnexionFormType::class);

        // Retourne la vue avec le formulaire
        return $this->render('pages/utilisateur/connexion.html.twig', [
            'offreNavbar' => true,
            'last_email' => $lastEmail,
            'error' => $error,
            'connexionForm' => $form->createView(),
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formRecherche' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
        ]);
    }
}