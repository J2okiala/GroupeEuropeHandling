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
        // Création d'une nouvelle candidature
        $candidature = new CandidatureSpontanee();
    
        // Création du formulaire
        $form = $this->createForm(CandidatureSpontaneeFormType::class, $candidature);
    
        // Gestion de la soumission du formulaire
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des fichiers
            $cvFile = $form->get('cv')->getData();
            $lmFile = $form->get('lm')->getData();
    
            // Vérification si les fichiers ont bien été téléchargés
            if ($cvFile && $lmFile) {
                // Générer un nom unique pour chaque fichier
                $cvFileName = uniqid() . '.' . $cvFile->guessExtension();
                $lmFileName = uniqid() . '.' . $lmFile->guessExtension();
    
                try {
                    // Déplacer les fichiers vers le répertoire "public/uploads/cvs"
                    $cvDestination = $this->getParameter('uploads_directory') . '/cvs';
                    $lmDestination = $this->getParameter('uploads_directory') . '/cvs';
    
                    // Créer le dossier s'il n'existe pas
                    if (!is_dir($cvDestination)) {
                        mkdir($cvDestination, 0777, true);
                    }
    
                    // Déplacer les fichiers
                    $cvFile->move($cvDestination, $cvFileName);
                    $lmFile->move($lmDestination, $lmFileName);
    
                    // Mettre à jour les propriétés 'cv' et 'lm' avec les chemins des fichiers
                    $candidature->setCv('cvs/' . $cvFileName);
                    $candidature->setLm('cvs/' . $lmFileName);
                } catch (FileException $e) {
                    // Gestion des erreurs en cas d'échec de l'upload
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement des fichiers.');
                    return $this->redirectToRoute('candidatureSpontanee');
                }
            }
    
            // Ajouter la date actuelle à la candidature
            $candidature->setDate(new \DateTime()); // Définit la date actuelle
    
            // Persister la candidature dans la base de données
            $dm->persist($candidature);
            $dm->flush();
    
            // Ajouter un message de succès et rediriger
            $this->addFlash('success', 'Votre candidature a été envoyée avec succès !');
            return $this->redirectToRoute('nosOffres');
        }
    
        // Rendu de la vue avec le formulaire
        return $this->render('pages/candidatureSpontanee/candidatureSpontanee.html.twig', [
            'form' => $form->createView(),
            'offreNavbar' => true,
            'withFiltrer' => false, // Pas de filtrage sur cette page
            'formRecherche' => null, // Passer null si tu ne veux pas que formRecherche soit utilisé
        ]);
    }
    
}