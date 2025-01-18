<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\HttpFoundation\Session\Session;

#[Route('/users')]
class UserController extends AbstractController
{
    /**
     * Affiche tous les utilisateurs
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        // Prépare la requête SQL pour le nombre de personne inscrite
        $query = $entityManager->createQuery(
            'SELECT COUNT(u)
            FROM App\Entity\User u'
        );

        // Execute la requête
        $result = $query->getResult();

        return $this->render('user/index.html.twig', [
            // Récupère tous les utilisateurs
            'users' => $userRepository->findAll(),
            'nbrUsers' => $result[0][1]
        ]);
    }

    /**
     * Affiche l'intégralité des données de l'utilisateur via son ID
     * @param User $user
     */
    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show($id, User $user, ?UserInterface $userConnecte, UserRepository $userrepo) : Response
    {
        $Attributes = $userrepo->findOneById($id);
        $properties = get_object_vars($Attributes);

        return $this->render('user/show.html.twig', [
            'user' => $user,
            //'properties' => $properties
        ]);
    }

    /**
     * Supprime le compte utilisateur via son ID
     * @param User $user
     * @param EntityManagerInterface $entityManager
     */
    #[Route('/delete/confirm', name: 'user_delete')]
    public function delete(?UserInterface $user, EntityManagerInterface $entityManager): Response
    {
        // Vérifie que l'utilisateur soit connecté
        if($user)
        {
            // Supprime la photo de profil si l'utilisateur avait une photo
            if($user->getProfilPhotoPath() != '-')
            {
                $oldProfilPhoto = $user->getProfilPhotoPath();
                $filesystem = new Filesystem();
                $filesystem->remove('/var/www/lavraiemifa/public/'.$oldProfilPhoto);
            }

            // Supprime l'utilisateur
            $entityManager->remove($user);
            $entityManager->flush();

            $session = new Session();
            $session->invalidate();
        }
        return $this->redirectToRoute('home');
    }
}
