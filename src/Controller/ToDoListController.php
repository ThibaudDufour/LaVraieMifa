<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ToDoTaskFormType;
use App\Repository\ToDoTaskRepository;
use App\Entity\ToDoTask;

class ToDoListController extends AbstractController {

    /**
     * Affiche la liste pour la To Do List et Did List
     * @param ToDoTaskRepository $taskRepo
     */
    #[Route('/todolist', name : 'todolist')]
    public function toDoList(ToDoTaskRepository $taskRepo) 
    {
        return $this->render('todo_list/todolist.html.twig', [
            // Récupère tous les éléments en triant par la date
            'tasks' => $taskRepo->findBy([], ['id' => 'DESC'])
        ]);
    }

    /**
     * Ajouter une idée dans la To Do List
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param ToDoTaskRepository $taskRepo
     */
    #[Route('/todolist/add', name :'addTask', methods : ['GET','POST'])]
    public function addTask(?UserInterface $user, EntityManagerInterface $entityManager, Request $request, ToDoTaskRepository $taskRepo)
    {
        // Vérifie que l'utilisateur soit connecté
        if ($user) {

            // Créer le formulaire pour ajouter une idée
            $form = $this->createForm(ToDoTaskFormType::class);
            $form->handleRequest($request);

            // Vérifie si le formualire soit bien envoyé et que le formualire soit valide
            if ($form->isSubmitted() && $form->isValid()) {
                // Prépare la requête SQL
                $task = new ToDoTask();
                $task->setDescription($form->get('task')->getData());
                $task->setUser($user);
                $task->setDate(new \DateTime());

                // Execute la requête SQL
                $entityManager->persist($task);
                $entityManager->flush();

                return $this->redirectToRoute('todolist');

            } else {
                return $this->render('todo_list/add_task.html.twig', [
                    'formTask' => $form->createView(),
                    'tasks' => $taskRepo->findBy([], ['id' => 'DESC'])
                ]);
            }
        }

        return $this->redirectToRoute('home');
    }

    /**
     * Supprimer un élément de la To Do List ou Did List
     * @param int $id
     * @param UserInterface $user
     * @param ToDoTaskRepository $taskrepo
     * @param EntityManagerInterface $entityManager
     */
    #[Route('/todolist/deleteTask/{id}', name : 'deleteTask')]
    public function deleteTask($id, ?UserInterface $user, ToDoTaskRepository $taskRepo, EntityManagerInterface $entityManager)
    {
        // Vérifie que l'utilisateur soit connecté
        if($user)
        {
            // Trouve l'élément à supprimer grâce à son id 
            $task = $taskRepo->find($id);

            // Supprime l'élément
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('todolist');
    }

    /**
     * Passe la tâche à "Non Fait" à "Fait"
     * To Do List --> Did List
     * @param int $id
     * @param UserInterface $user
     * @param ToDoTaskRepository $taskrepo
     * @param EntityManagerInterface $entityManager
     */
    #[Route('/todolist/isDone/{id}', name : 'doneTask')]
    public function doneTask($id, ?UserInterface $user, ToDoTaskRepository $taskRepo, EntityManagerInterface $entityManager)
    {
        // Vérifie que l'utilisateur soit connecté
        if($user)
        {
            // Trouve la tâche dans la BDD grâce à l'id
            $task = $taskRepo->find($id);

            // Met la tâche à "Effectuée"
            $task->setIsDone(true);
            $entityManager->flush();
        }
        return $this->redirectToRoute('todolist');
    }

    /**
     * Passe la tâche de "Fait" à "Non Fait"
     * Did List --> To Do List
     * @param int $id
     * @param UserInterface $user
     * @param ToDoTaskRepository $taskRepo
     * @param EntityManagerInterface $entityManager
     */
    #[Route('/todolist/isDidntDone/{id}', name : 'didntDoneTask')]
    public function didntDoneTask($id, ?UserInterface $user, ToDoTaskRepository $taskRepo, EntityManagerInterface $entityManager)
    {
        // Vérifie que l'utilisateur soit connecté
        if($user)
        {
            // Trouve la tâche dans la BDD grâce à l'id
            $task = $taskRepo->find($id);

            // Met la tâche à "Non effectuée"
            $task->setIsDone(false);
            $entityManager->flush();
        }
        return $this->redirectToRoute('todolist');
    }
}