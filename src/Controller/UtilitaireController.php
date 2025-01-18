<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/utilitaire')]
class UtilitaireController extends AbstractController
{
    /**
     * Affiche la bibliothèque "Utilitaire" regroupant drive, todolist, convertisseur, ...
     */
    #[Route('/', name: 'app_utilitaire')]
    public function index(): Response
    {
        return $this->render('utilitaire/index.html.twig', [
        ]);
    }
}
