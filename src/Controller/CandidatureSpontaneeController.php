<?php

namespace App\Controller;

use App\Document\CandidatureSpontanee;
use App\Form\CandidatureSpontaneeFormType;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CandidatureSpontaneeController extends AbstractController
{
    #[Route('/candidatureSpontanee', name: 'candidatureSpontanee')]
    public function candidature(Request $request, DocumentManager $dm): Response
    {
        // Rediriger l'utilisateur vers la page de profil si il est connecté
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('profilCandidat');
        }

        // Création d'une nouvelle candidature
        $candidature = new CandidatureSpontanee();

        // Création du formulaire
        $form = $this->createForm(CandidatureSpontaneeFormType::class, $candidature);

        // Gestion de la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion des fichiers uploadés
            $cvFile = $form->get('cv')->getData();
            $lmFile = $form->get('lm')->getData();

            if ($cvFile && $lmFile) {
                // Générer un nom unique pour chaque fichier
                $cvFileName = uniqid() . '.' . $cvFile->guessExtension();
                $lmFileName = uniqid() . '.' . $lmFile->guessExtension();

                try {
                    // Déplace les fichiers vers le répertoire défini (public/uploads)
                    $cvFile->move($this->getParameter('uploads_directory'), $cvFileName);
                    $lmFile->move($this->getParameter('uploads_directory'), $lmFileName);

                    // Met à jour les chemins des fichiers dans l'objet Candidature
                    $candidature->setCv($cvFileName);
                    $candidature->setLm($lmFileName);
                } catch (FileException $e) {
                    // Gérer les erreurs en cas d'échec de l'upload
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload des fichiers.');
                    return $this->redirectToRoute('candidature');
                }
            }

            // Persister la candidature dans la base de données
            $dm->persist($candidature);
            $dm->flush();

            // Ajouter un message de succès et rediriger
            $this->addFlash('success', 'Votre candidature a été envoyée avec succès !');
            return $this->redirectToRoute('nosOffres');
        }

        // Rendu de la vue avec le formulaire
        return $this->render('pages/candidatureSpontanee/index.html.twig', [
            'form' => $form->createView(), 
            'offreNavbar' => true,
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formRecherche' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
        ]);
    }
}
