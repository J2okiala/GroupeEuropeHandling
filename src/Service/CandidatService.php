<?php

namespace App\Service;

use App\Entity\Candidat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CandidatService
{
    private $entityManager;
    private $uploadDirectory;

    /**
     * @param EntityManagerInterface $entityManager
     * @param string $uploadDirectory
     */
    public function __construct(EntityManagerInterface $entityManager, string $uploadDirectory)
    {
        $this->entityManager = $entityManager;
        $this->uploadDirectory = $uploadDirectory;
    }

    /**
     * Modifie les informations du candidat
     * @param Candidat $candidat
     * @param UploadedFile|null $cvFile
     * @param UploadedFile|null $lettreMotivationFile
     * 
     * @return void
     */
    public function updateCandidat(Candidat $candidat, ?UploadedFile $cvFile, ?UploadedFile $lettreMotivationFile): void
    {
        if ($cvFile) {
            $cvFilename = uniqid().'.'.$cvFile->guessExtension();
            try {
                $cvFile->move($this->uploadDirectory, $cvFilename);
                $candidat->setCv($cvFilename);
            } catch (FileException $e) {
                throw new \Exception('Erreur lors de l\'upload du CV.');
            }
        }

        if ($lettreMotivationFile) {
            $lettreMotivationFilename = uniqid().'.'.$lettreMotivationFile->guessExtension();
            try {
                $lettreMotivationFile->move($this->uploadDirectory, $lettreMotivationFilename);
                $candidat->setLettreMotivation($lettreMotivationFilename);
            } catch (FileException $e) {
                throw new \Exception('Erreur lors de l\'upload de la lettre de motivation.');
            }
        }

        $utilisateur = $candidat->getUtilisateur();
        $utilisateur->setNom($candidat->getNom());
        $utilisateur->setPrenom($candidat->getPrenom());

        $this->entityManager->flush();
    }
}
