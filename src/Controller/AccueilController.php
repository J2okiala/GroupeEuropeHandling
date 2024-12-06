<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class AccueilController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home()
    {
        return $this->render('pages/home/index.html.twig');

    }

    #[Route('/nosServices', name: 'nosServices')]
    public function nosServices()
    {
        return $this->render('pages/home/nosServices.html.twig');

    }

    #[Route('/nosOffres', name: 'nosOffres')]
    public function nosOffres()
    {
        return $this->render('pages/home/nosOffres.html.twig', [
            'isSecondaryNavbar' => true,
        ]);
    }

    #[Route('/nosChiffres', name: 'nosChiffres')]
    public function chiffres()
    {
        return $this->render('pages/home/nosChiffres.html.twig');

    }
}