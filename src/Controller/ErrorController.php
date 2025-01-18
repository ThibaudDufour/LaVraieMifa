<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class ErrorController extends AbstractController
{
    /**
     * @Route("/error", name="error")
     * 
     * Page d'erreur
     * 
     * @param UserInterface $user
     * @param FlattenException $execption
     * @param DebugLoggerInterface $logger
     */
    public function show(?UserInterface $user, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        switch($exception->getStatusCode()){
            case 401:
            case 403:
                $titre = "Non autorisé";
                $message = "Cet accès demande des accès de privilèges dont vous ne disposez pas";
                break;
            case 404:
                $titre = "On dirait que vous êtes perdu ... ";
                $message = "La page que vous recherchez n'est pas disponible !";
                break;
            case 405:
                $titre = "Méthode non autorisée";
                $message = "La méthode que vous utilisez n'est pas prise en charge";
                break;
            default:
                $titre = "Une erreur est survenue ...";
                $message = "Il est impossible de réaliser votre demande pour le moment !\nLes administrateurs sont sur le coup 😉";

        }
        return $this->render('error.html.twig', [
            'code' => $exception->getStatusCode(),
            'titre' => $titre,
            'message' => $message
        ]);
    }
}