<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Util\TargetPathTrait;


class AboutUsController extends AbstractController
{
    /**
     * Route principale pour afficher la page "A propos de nous"
     * @param UserRepository $userRepository
     */
    #[Route('/about-us', name: 'app_about_us')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('about_us/index.html.twig', [
            // Liste de tous les utilisateurs
            'users' => $userRepository->findAll()
        ]);
    }
}
