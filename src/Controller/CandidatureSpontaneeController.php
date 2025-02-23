<?php

namespace App\Controller;

use App\Document\CandidatureSpontanee;
use App\Form\CandidatureSpontaneeFormType;
use App\Repository\CandidatureSpontaneeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CandidatureSpontaneeController extends AbstractController
{
    #[Route('/candidatureSpontanee', name: 'candidatureSpontanee')]
    public function candidature(Request $request, CandidatureSpontaneeRepository $repository): Response
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
                    $cvDestination = $this->getParameter('uploads_directory');
                    $lmDestination = $this->getParameter('uploads_directory');

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
            $candidature->setDate(new \DateTime());

            // Utilisation de la méthode save du repository
            $repository->save($candidature);

            // Ajouter un message de succès et rediriger
            $this->addFlash('success', 'Votre candidature a été envoyée avec succès !');
            return $this->redirectToRoute('nosOffres');
        }

        return $this->render('pages/candidatureSpontanee/candidatureSpontanee.html.twig', [
            'form' => $form->createView(),
            'offreNavbar' => true,
            'withFiltrer' => false,
            'formRecherche' => null,
        ]);
    }
}
