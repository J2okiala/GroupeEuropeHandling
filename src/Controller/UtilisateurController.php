<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Candidat;
use App\Form\ConnexionFormType;
use App\Form\InscriptionFormType;
use App\Repository\UtilisateurRepository;
use App\Repository\CandidatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UtilisateurController extends AbstractController
{
    #[Route('/inscription', name: 'inscription')]
    public function inscription(
        Request $request, 
        UtilisateurRepository $utilisateurRepository, 
        CandidatRepository $candidatRepository, 
        UserPasswordHasherInterface $passwordHasher
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

                // Ajoute le rôle CANDIDAT par défaut
                $utilisateur->setRoles(['ROLE_CANDIDAT']);

                // Crée et associe une entité Candidat à l'utilisateur
                $candidat = new Candidat();
                $candidat->setUtilisateur($utilisateur);
                $candidatRepository->save($candidat, true);

                // Sauvegarde l'utilisateur dans la base de données
                $utilisateurRepository->save($utilisateur, true);

                // Ajoute un message de succès
                $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');

                // Redirige après l'inscription
                return $this->redirectToRoute('nosOffres');
            }
        }

        return $this->render('pages/utilisateur/inscription.html.twig', [
            'isSecondaryNavbar' => true,
            'registrationForm' => $form->createView(),
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
            'isSecondaryNavbar' => true,
            'last_email' => $lastEmail,
            'error' => $error,
            'connexionForm' => $form->createView(),
        ]);
    }
}
