<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilFormType;
use App\Security\UserAuthenticator;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class ProfilController extends AbstractController
{
    /**
     * Affiche, modifie et sauvegarde les profils
     * 
     * @param UserInterface $user
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param UserAuthenticatorInterface $userAuthenticator
     * @param EntityManagerInterface $entityManager
     * @param SluggerInterface $slugger
     * 
     */
    #[Route('/profil', name: 'profil')]
    public function index(?UserInterface $user, Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Vérifie que l'utilisateur soit connecté
        if($user)
        {
            // Créer le forumlaire
            $form = $this->createForm(ProfilFormType::class, $user);
            $form->handleRequest($request);

            // Vérifie que le formulaire soit valide est envoyé
            if ($form->isSubmitted() && $form->isValid()) {

                // Récupère la photo de profil
                $profilPhotoPath = $form->get('profilPhoto')->getData();
                
                // Vérifie l'exitence de la photo de profil
                if ($profilPhotoPath) {

                    // Supprime l'ancienne photo de profil si une nouvelle existe
                    if($user->getProfilPhotoPath() != '-')
                    {
                        $oldProfilPhoto = $user->getProfilPhotoPath();
                        $filesystem = new Filesystem();
                        $filesystem->remove('/var/www/lavraiemifa/public'.$oldProfilPhoto);
                    }

                    // Prépare le transfert de la photo de profil
                    $originalFilename = pathinfo($profilPhotoPath->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = "/images/user/".$safeFilename.'-'.uniqid().'.'.$profilPhotoPath->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $profilPhotoPath->move(
                            $this->getParameter('profilPhotoPath_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $user->setProfilPhotoPath($newFilename);
                }
                
                // Récupère le nouveau mot de passe
                $newPassword = $form->get('password')->getData();
                if($newPassword != null)
                {
                    // Vérifie l'ancien mot de passe 
                    $oldPassword = $form->get('old_pwd')->getData();
                    if ($oldPassword != null) {
                        if ($userPasswordHasher->isPasswordValid($user, $oldPassword)) {

                            // Met le nouveau mot de passe
                            $user->setPassword(
                                $userPasswordHasher->hashPassword(
                                    $user,
                                    $newPassword
                                )
                            );
                            $request->getSession()->set('newMdP', true);
                        } else {
                            $error = 'Votre ancien mot de passe est incorrect';
                            return $this->render('profil/profil.html.twig', [
                                'error' => $error,
                                'profilForm' => $form->createView()
                            ]);
                        }
                    } else {
                        $error = 'Vous devez retaper votre ancien mot de passe pour pouvoir le modifier';
                            return $this->render('profil/profil.html.twig', [
                                'error' => $error,
                                'profilForm' => $form->createView()
                            ]);
                    }
                }
                $entityManager->persist($user);
                $entityManager->flush();

                return $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
                );
            }

            return $this->render('profil/profil.html.twig', [
                'profilForm' => $form->createView(),
            ]);
        }
        else{
            return $this->redirectToRoute('home');
        } 
    }

    #[Route('/profil/{memberId}', name: 'member_profil')]
    public function view($memberId, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $member = $userRepository->findBy(['id'=>$memberId]);

        if(!empty($member)) {

        } else {
            //Retourne erreur 404
            return $this->redirectToRoute('home');
        }
    }
}
