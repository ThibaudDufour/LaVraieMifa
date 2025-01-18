<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Repository\ScoreUserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

#[Route("/games")]
class GamesController extends AbstractController
{
    /**
     * Affiche une bibliothèque de jeu
     */
    #[Route("/", name: "app_games")]
    public function games()
    {
        return $this->render('games/index.html.twig', []);
    }


    /**
     * Affiche le jeu Tétris
     * @param UserInterface $user
     * @param UserRepository $userRepository
     * @param EntityManager $entityManager
     */
    #[Route("/tetris", name: "app_tetris")]
    public function tetris(?UserInterface $user, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        // Récupère tous les joueurs en les triant par leur score
        $orderedUsers = $userRepository->findBy([], ['scoreTetris' => 'DESC']);

        // On récupère le premier joueur 
        $bestUser = $orderedUsers[0]; 
        $bestScore = $bestUser->getScoreTetris();
        $bestUserName = $bestUser->getUsername();

        // Récupère le score du joueur connecté
        if ($user){
            $bestScorePerso = $user->getScoreTetris();
            $entityManager->persist($user);
        }
        else 
            $bestScorePerso = -1;

        return $this->render('games/tetris.html.twig', [
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'orderedUsers' => $orderedUsers
        ]);
        
    }

    /**
     * Affiche le jeu snake
     * @param UserRepository $userRepository
     * @param userInterface $user
     */
    #[Route("/snake", name: "app_snake")]
    public function snake(UserRepository $userRepository, ?UserInterface $user)
    {
        // Récupère tous les joueurs en les triant par leur score
        $orderedUsers = $userRepository->findBy([], ['scoreSnake' => 'DESC']);

        // On récupère le premier joueur 
        $bestUser = $orderedUsers[0];
        $bestScore = $bestUser->getScoreSnake();
        $bestUserName = $bestUser->getUsername();

        // Récupère le score du joueur connecté
        if ($user) 
            $bestScorePerso = $user->getScoreSnake();
        else 
            $bestScorePerso = -1;

        return $this->render('games/snake.html.twig', [
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'orderedUsers' => $orderedUsers
        ]);
        
    }

    /**
     * Affiche le jeu marie
     */
    #[Route("/marie", name: "app_marie")]
    public function marie()
    {
        return $this->render('games/marie.html.twig', []);
    }

    /**
     * Affiche le démineur
     */
    #[Route("/demineur", name: "app_demineur")]
    public function demineur()
    {
        return $this->render('games/demineur/index.html.twig', []);
    }

    /**
     * Affiche le jeu démineur en focntion de la difficulté sélectionné
     */
    #[Route("/demineur/jeu", name: "jeu")]
    public function jeu()
    {
        return $this->render('games/demineur/jeu.html.twig', ['difficulte' => $_GET["difficulte"]]);
    }

    /**
     * Affiche le jeu 2048
     * @param UserRepository $userRepository
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     */
    #[Route("/2048", name: "app_2048")]
    public function jeu2048(UserRepository $userRepository, ?UserInterface $user, EntityManagerInterface $entityManager)
    {
        // Récupère tous les joueurs en les triant par leur score
        $orderedUsers = $userRepository->findBy([], ['score2048' => 'DESC']);

        // On récupère le premier joueur 
        $bestUser = $orderedUsers[0];
        $bestScore = $bestUser->getScore2048();
        $bestUserName = $bestUser->getUsername();

        // Récupère le score du joueur connecté
        if ($user){
            $bestScorePerso = $user->getScore2048();
            $entityManager->persist($user);
        }
        else 
            $bestScorePerso = -1;

        return $this->render('games/2048.html.twig', [
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'orderedUsers' => $orderedUsers
        ]);
    }

    /**
     * Affiche le jeu FlappyBird
     * @param UserRepository $userRepository
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     */
    #[Route("/flappybird", name: "app_flappybird")]
    public function flappyBird(UserRepository $userRepository, ?UserInterface $user, EntityManagerInterface $entityManager)
    {
        // Récupère tous les joueurs en les triant par leur score
        $orderedUsers = $userRepository->findBy([], ['scoreFlappyBird' => 'DESC']);

        // On récupère le premier joueur
        $bestUser = $orderedUsers[0];
        $bestScore = $bestUser->getScoreFlappyBird();
        $bestUserName = $bestUser->getUsername();

        // Récupère le score du joueur connecté
        if ($user){
            $bestScorePerso = $user->getScoreFlappyBird();
            $entityManager->persist($user);
        }
        else 
            $bestScorePerso = -1;

        return $this->render('games/flappybird.html.twig', [
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'orderedUsers' => $orderedUsers
        ]);
    }

    /**
     * Affiche le jeu PacMan
     * @param UserRepository $userRepository
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     */
    #[Route("/pacman", name: "app_pacman")]
    public function pacman(UserRepository $userRepository, ?UserInterface $user, EntityManagerInterface $entityManager)
    {
        // Récupère tous les joueurs en les triant par leur score
        $orderedUsers = $userRepository->findBy([], ['scorePacMan' => 'DESC']);

        // On récupère le premier joueur
        $bestUser = $orderedUsers[0];
        $bestScore = $bestUser->getScorePacMan();
        $bestUserName = $bestUser->getUsername();

        // Récupère le score du joueur connecté
        if ($user){
            $bestScorePerso = $user->getScorePacMan();
            $entityManager->persist($user);
        }
        else 
            $bestScorePerso = -1;

        return $this->render('games/pacman.html.twig', [
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'orderedUsers' => $orderedUsers
        ]);
    }

    /**
     * Affiche le jeu Space Invaders
     * @param UserRepository $userRepository
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     */
    #[Route("/space-invaders", name: "app_space_invaders")]
    public function spaceInvaders(UserRepository $userRepository, ?UserInterface $user, EntityManagerInterface $entityManager)
    {
        // Récupère tous les joueurs en les triant par leur score
        $orderedUsers = $userRepository->findBy([], ['scoreSpaceInvaders' => 'DESC']);

        // On récupère le premier joueur
        $bestUser = $orderedUsers[0];
        $bestScore = $bestUser->getScoreSpaceInvaders();
        $bestUserName = $bestUser->getUsername();

        // Récupère le score du joueur connecté
        if ($user){
            $bestScorePerso = $user->getScoreSpaceInvaders();
            $entityManager->persist($user);
        }
        else 
            $bestScorePerso = -1;

        return $this->render('games/spaceinvaders.html.twig', [
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'orderedUsers' => $orderedUsers
        ]);
    }

    /**
     * Affiche le jeu Bubble Shooter
     * @param UserRepository $userRepository
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     */
    #[Route("/bubble-shooter", name: "app_bubble_shooter")]
    public function bubbleShooter(UserRepository $userRepository, ?UserInterface $user, EntityManagerInterface $entityManager)
    {
        // Récupère tous les joueurs en les triant par leur score
        $orderedUsers = $userRepository->findBy([], ['scoreBubbleShooter' => 'DESC']);

        // On récupère le premier joueur
        $bestUser = $orderedUsers[0];
        $bestScore = $bestUser->getScoreBubbleShooter();
        $bestUserName = $bestUser->getUsername();

        // Récupère le score du joueur connecté
        if ($user){
            $bestScorePerso = $user->getScoreBubbleShooter();
            $entityManager->persist($user);
        }
        else 
            $bestScorePerso = -1;

        return $this->render('games/bubbleshooter.html.twig', [
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'orderedUsers' => $orderedUsers
        ]);
    }

    /**
     * Affiche le jeu Jurassic Park
     * @param UserRepository $userRepository
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     */
    #[Route("/jurassic-park", name: "app_jurassic_park")]
    public function jurassicPark(UserRepository $userRepository, ?UserInterface $user, EntityManagerInterface $entityManager)
    {
        // Récupère tous les joueurs en les triant par leur score
        $orderedUsers = $userRepository->findBy([], ['scoreJurassicPark' => 'DESC']);

        // On récupère le premier joueur
        $bestUser = $orderedUsers[0];
        $bestScore = $bestUser->getScoreJurassicPark();
        $bestUserName = $bestUser->getUsername();

        // Récupère le score du joueur connecté
        if ($user){
            $bestScorePerso = $user->getScoreJurassicPark();
            $entityManager->persist($user);
        }
        else 
            $bestScorePerso = -1;

        return $this->render('games/trex.html.twig', [
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'orderedUsers' => $orderedUsers
        ]);
    }

    /**
     * Affiche le jeu Doodle Jump
     * @param UserRepository $userRepository
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     */
    #[Route("/doodle-jump", name: "app_doodle_jump")]
    public function doodleJump(UserRepository $userRepository, ?UserInterface $user, EntityManagerInterface $entityManager)
    {
        // Récupère tous les joueurs en les triant par leur score
        $orderedUsers = $userRepository->findBy([], ['scoreDoodleJump' => 'DESC']);

        // On récupère le premier joueur
        $bestUser = $orderedUsers[0];
        $bestScore = $bestUser->getScoreDoodleJump();
        $bestUserName = $bestUser->getUsername();

        // Récupère le score du joueur connecté
        if ($user){
            $bestScorePerso = $user->getScoreDoodleJump();
            $entityManager->persist($user);
        }
        else 
            $bestScorePerso = -1;

        return $this->render('games/doodlejump.html.twig', [
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'orderedUsers' => $orderedUsers
        ]);
    }




    /**
     * API - Compare et sauvegarde le score tetris du joueur connecté
     * @param int $newScore
     * @param UserInterface $user
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @return JsonReponse
     */
    #[Route('/tetris/score/current/{newScore}', name : 'scoreTetris', methods: ['GET'])]
    public function compareScoresTetris(int $newScore, ?UserInterface $user, UserRepository $userRepository, EntityManagerInterface $entityManager) {

        // Récupère tous les joueurs en trian les scores
        $orderedUsers = $userRepository->findBy([], ['scoreTetris' => 'DESC']);

        // On récupère le premier joueur
        $bestUser = $orderedUsers[0];
        $bestScore = $bestUser->getScoreTetris();
        $bestUserName = $bestUser->getUsername();
        
        // Vérifie que l'utilisateur soit connecté
        if ($user) {
            // Récupère le score du joueur
            $bestScorePerso = $user->getScoreTetris();

            // Sauvegarde le score du joueur si celui-ci est plus grand que celui stocké
            if ($newScore > $bestScorePerso) {
                $bestScorePerso = $newScore;
                $user->setScoreTetris($newScore);
                $entityManager->persist($user);
                $entityManager->flush();
                if ($newScore > $bestScore) {
                    $bestScore = $newScore;
                    $bestUser = $user;
                    $bestUserName = $bestUser->getUsername();
                }
            }
        } else {
            $bestScorePerso = -1;
        }  

        return new JsonResponse([
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'input_score' => $newScore
        ]);
    }

    /**
     * API - Compare et sauvegarde le score snake du joueur connecté
     * @param int $newScore
     * @param UserRepository $userRepository
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/snake/score/current/{newScore}', name : 'scoreSnake', methods: ['GET'])]
    public function compareScoresSnake(int $newScore, UserRepository $userRepository, ?UserInterface $user, EntityManagerInterface $entityManager) {
        
        // Récupère tous les joueurs en trian les scores
        $orderedUsers = $userRepository->findBy([], ['scoreSnake' => 'DESC']);

        // On récupère le premier joueur
        $bestUser = $orderedUsers[0];
        $bestScore = $bestUser->getScoreSnake();
        $bestUserName = $bestUser->getUsername();
        
        // Vérifie que l'utilisateur soit connecté
        if ($user) {
            // Récupère le score du joueur
            $bestScorePerso = $user->getScoreSnake();

            // Sauvegarde le score du joueur si celui-ci est plus grand que celui stocké
            if ($newScore > $bestScorePerso) {
                $bestScorePerso = $newScore;
                $user->setScoreSnake($newScore);
                $entityManager->flush();
                $entityManager->persist($user);
                if ($newScore > $bestScore) {
                    $bestScore = $newScore;
                    $bestUser = $user;
                    $bestUserName = $bestUser->getUsername();
                }
            }
        } else {
            $bestScorePerso = -1;
        }  

        return new JsonResponse([
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'input_score' => $newScore
        ]);
    }

    /**
     * API - Compare et sauvegarde le score 2048 du joueurs connecté
     * @param int $newScore
     * @param UserRepository $userRepository
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/2048/score/current/{newScore}', name : 'score2048', methods: ['GET'])]
    public function compareScores2048(int $newScore, UserRepository $userRepository, ?UserInterface $user, EntityManagerInterface $entityManager) {
        
        // Récupère tous les joueurs en trian les scores
        $orderedUsers = $userRepository->findBy([], ['score2048' => 'DESC']);

        // On récupère le premier joueur
        $bestUser = $orderedUsers[0];
        $bestScore = $bestUser->getScore2048();
        $bestUserName = $bestUser->getUsername();
        
        // Vérifie que l'utilisateur soit connecté
        if ($user) {
            // Récupère le score du joueur
            $bestScorePerso = $user->getScore2048();

            // Sauvegarde le score du joueur si celui-ci est plus grand que celui stocké
            if ($newScore > $bestScorePerso) {
                $bestScorePerso = $newScore;
                $user->setScore2048($newScore);
                $entityManager->flush();
                $entityManager->persist($user);
                if ($newScore > $bestScore) {
                    $bestScore = $newScore;
                    $bestUser = $user;
                    $bestUserName = $bestUser->getUsername();
                }
            }
        } else {
            $bestScorePerso = -1;
        }  

        return new JsonResponse([
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'input_score' => $newScore
        ]);
    }

    /**
     * API - Compare et sauvegarde le score FlappyBird du joueur connecté
     * @param int $newScore
     * @param UserRespository $userRepository
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/flappybird/score/current/{newScore}', name : 'scoreFlappyBird', methods: ['GET'])]
    public function compareScoresFlappyBird(int $newScore, UserRepository $userRepository, ?UserInterface $user, EntityManagerInterface $entityManager) {
        
        // Récupère tous les joueurs en trian les scores
        $orderedUsers = $userRepository->findBy([], ['scoreFlappyBird' => 'DESC']);

        // On récupère le premier joueur
        $bestUser = $orderedUsers[0];
        $bestScore = $bestUser->getScoreFlappyBird();
        $bestUserName = $bestUser->getUsername();
        
        // Vérifie que l'utilisateur soit connecté
        if ($user) {
            // Récupère le score du joueur
            $bestScorePerso = $user->getScoreFlappyBird();

            // Sauvegarde le score du joueur si celui-ci est plus grand que celui stocké
            if ($newScore > $bestScorePerso) {
                $bestScorePerso = $newScore;
                $user->setScoreFlappyBird($newScore);
                $entityManager->flush();
                $entityManager->persist($user);
                if ($newScore > $bestScore) {
                    $bestScore = $newScore;
                    $bestUser = $user;
                    $bestUserName = $bestUser->getUsername();
                }
            }
        } else {
            $bestScorePerso = -1;
        }  

        return new JsonResponse([
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'input_score' => $newScore
        ]);
    }

    /**
     * API - Compare et sauvegarde le score PacMan du joueur connecté
     * @param int $newScore
     * @param UserRespository $userRepository
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/pacman/score/current/{newScore}', name : 'scorePacMan', methods: ['GET'])]
    public function compareScoresPacMan(int $newScore, UserRepository $userRepository, ?UserInterface $user, EntityManagerInterface $entityManager) {
        
        // Récupère tous les joueurs en trian les scores
        $orderedUsers = $userRepository->findBy([], ['scorePacMan' => 'DESC']);

        // On récupère le premier joueur
        $bestUser = $orderedUsers[0];
        $bestScore = $bestUser->getScorePacMan();
        $bestUserName = $bestUser->getUsername();
        
        // Vérifie que l'utilisateur soit connecté
        if ($user) {
            // Récupère le score du joueur
            $bestScorePerso = $user->getScorePacMan();

            // Sauvegarde le score du joueur si celui-ci est plus grand que celui stocké
            if ($newScore > $bestScorePerso) {
                $bestScorePerso = $newScore;
                $user->setScorePacMan($newScore);
                $entityManager->flush();
                $entityManager->persist($user);
                if ($newScore > $bestScore) {
                    $bestScore = $newScore;
                    $bestUser = $user;
                    $bestUserName = $bestUser->getUsername();
                }
            }
        } else {
            $bestScorePerso = -1;
        }  

        return new JsonResponse([
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'input_score' => $newScore
        ]);
    }

    /**
     * API - Compare et sauvegarde le score Space Invaders du joueur connecté
     * @param int $newScore
     * @param UserRespository $userRepository
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/space-invaders/score/current/{newScore}', name : 'scoreSpaceInvaders', methods: ['GET'])]
    public function compareScoresSpaceInvaders(int $newScore, UserRepository $userRepository, ?UserInterface $user, EntityManagerInterface $entityManager) {
        
        // Récupère tous les joueurs en trian les scores
        $orderedUsers = $userRepository->findBy([], ['scoreSpaceInvaders' => 'DESC']);

        // On récupère le premier joueur
        $bestUser = $orderedUsers[0];
        $bestScore = $bestUser->getScoreSpaceInvaders();
        $bestUserName = $bestUser->getUsername();
        
        // Vérifie que l'utilisateur soit connecté
        if ($user) {
            // Récupère le score du joueur
            $bestScorePerso = $user->getScoreSpaceInvaders();

            // Sauvegarde le score du joueur si celui-ci est plus grand que celui stocké
            if ($newScore > $bestScorePerso) {
                $bestScorePerso = $newScore;
                $user->setScoreSpaceInvaders($newScore);
                $entityManager->flush();
                $entityManager->persist($user);
                if ($newScore > $bestScore) {
                    $bestScore = $newScore;
                    $bestUser = $user;
                    $bestUserName = $bestUser->getUsername();
                }
            }
        } else {
            $bestScorePerso = -1;
        }  

        return new JsonResponse([
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'input_score' => $newScore
        ]);
    }

    /**
     * API - Compare et sauvegarde le score Bubble Shooter du joueur connecté
     * @param int $newScore
     * @param UserRespository $userRepository
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/bubble-shooter/score/current/{newScore}', name : 'scoreBubbleShooter', methods: ['GET'])]
    public function compareScoresBubbleShooter(int $newScore, UserRepository $userRepository, ?UserInterface $user, EntityManagerInterface $entityManager) {
        
        // Récupère tous les joueurs en trian les scores
        $orderedUsers = $userRepository->findBy([], ['scoreBubbleShooter' => 'DESC']);

        // On récupère le premier joueur
        $bestUser = $orderedUsers[0];
        $bestScore = $bestUser->getScoreBubbleShooter();
        $bestUserName = $bestUser->getUsername();
        $oldscore = -1;
        
        // Vérifie que l'utilisateur soit connecté
        if ($user) {
            // Récupère le score du joueur
            $bestScorePerso = $user->getScoreBubbleShooter();
            $oldscore = $bestScorePerso;

            // Sauvegarde le score du joueur si celui-ci est plus grand que celui stocké
            if ($newScore > $bestScorePerso) {
                $bestScorePerso = $newScore;
                $user->setScoreBubbleShooter($newScore);
                $entityManager->flush();
                $entityManager->persist($user);
                if ($newScore > $bestScore) {
                    $bestScore = $newScore;
                    $bestUser = $user;
                    $bestUserName = $bestUser->getUsername();
                }
            }
        } else {
            $bestScorePerso = -1;
        }  

        return new JsonResponse([
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'old_best_score_perso' => $oldscore
        ]);
    }

    /**
     * API - Compare et sauvegarde le score Jurassic Park du joueur connecté
     * @param int $newScore
     * @param UserRespository $userRepository
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/jurassic-park/score/current/{newScore}', name : 'scoreJurassicPark', methods: ['GET'])]
    public function compareScoresJurassicPark(int $newScore, UserRepository $userRepository, ?UserInterface $user, EntityManagerInterface $entityManager) {
        
        // Récupère tous les joueurs en trian les scores
        $orderedUsers = $userRepository->findBy([], ['scoreJurassicPark' => 'DESC']);

        // On récupère le premier joueur
        $bestUser = $orderedUsers[0];
        $bestScore = $bestUser->getScoreJurassicPark();
        $bestUserName = $bestUser->getUsername();
        
        // Vérifie que l'utilisateur soit connecté
        if ($user) {
            // Récupère le score du joueur
            $bestScorePerso = $user->getScoreJurassicPark();

            // Sauvegarde le score du joueur si celui-ci est plus grand que celui stocké
            if ($newScore > $bestScorePerso) {
                $bestScorePerso = $newScore;
                $user->setScoreJurassicPark($newScore);
                $entityManager->flush();
                $entityManager->persist($user);
                if ($newScore > $bestScore) {
                    $bestScore = $newScore;
                    $bestUser = $user;
                    $bestUserName = $bestUser->getUsername();
                }
            }
        } else {
            $bestScorePerso = -1;
        }  

        return new JsonResponse([
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'input_score' => $newScore
        ]);
    }

    /**
     * API - Compare et sauvegarde le score Doodle Jump du joueur connecté
     * @param int $newScore
     * @param UserRespository $userRepository
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/doodle-jump/score/current/{newScore}', name : 'scoreDoodleJump', methods: ['GET'])]
    public function compareScoresDoodleJump(int $newScore, UserRepository $userRepository, ?UserInterface $user, EntityManagerInterface $entityManager) {
        
        // Récupère tous les joueurs en trian les scores
        $orderedUsers = $userRepository->findBy([], ['scoreDoodleJump' => 'DESC']);

        // On récupère le premier joueur
        $bestUser = $orderedUsers[0];
        $bestScore = $bestUser->getScoreDoodleJump();
        $bestUserName = $bestUser->getUsername();
        
        // Vérifie que l'utilisateur soit connecté
        if ($user) {
            // Récupère le score du joueur
            $bestScorePerso = $user->getScoreDoodleJump();

            // Sauvegarde le score du joueur si celui-ci est plus grand que celui stocké
            if ($newScore > $bestScorePerso) {
                $bestScorePerso = $newScore;
                $user->setScoreDoodleJump($newScore);
                $entityManager->flush();
                $entityManager->persist($user);
                if ($newScore > $bestScore) {
                    $bestScore = $newScore;
                    $bestUser = $user;
                    $bestUserName = $bestUser->getUsername();
                }
            }
        } else {
            $bestScorePerso = -1;
        }  

        return new JsonResponse([
            'best_score' => $bestScore,
            'best_player' => $bestUserName,
            'best_score_perso' => $bestScorePerso,
            'input_score' => $newScore
        ]);
    }
}
