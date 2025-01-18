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

use App\Repository\ToDoTaskRepository;
use App\Entity\ToDoTask;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * Affiche la HomePage
     */
    public function home(UserRepository $userRepository, ToDoTaskRepository $taskRepo)
    {
        return $this->render('index.html.twig', [
            'users' => $userRepository->findAll(),
            'tasks' => $taskRepo->findNewToDoTask()
        ]);
    }
}