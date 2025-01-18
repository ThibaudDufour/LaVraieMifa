<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\NewsFeedFormType;
use App\Repository\NewsFeedRepository;
use App\Entity\NewsFeed;
use App\Entity\NewsFeedLike;
use App\Repository\NewsFeedLikeRepository;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use Symfony\Component\Mime\Address;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

#[Route('/news-feed')]
class NewsFeedController extends AbstractController
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Affiche les posts et ajoute un post
     *
     * @param UserInterface|null $user
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param NewsFeedRepository $newsfeedRepo
     * @return Response
     */
    #[Route('/', name: 'app_news_feed')]
    public function aindex(?UserInterface $user, EntityManagerInterface $entityManager, Request $request, NewsFeedRepository $newsfeedRepo): Response
    {
        // Créer le formulaire pour récupérer le lien 
        $form = $this->createForm(NewsFeedFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $typeSocialMedia = $form->get('typeSocialMedia')->getData();
            if($form->get('contentMessage')->getData() == null)
                $contentMessage = '';
            else
                $contentMessage = $form->get('contentMessage')->getData();

            // Vérifie que des données existes
            if($typeSocialMedia != '' || $form->get('lien')->getData() != ''){

                // Prepare l'url pour appeler l'API pour "tranformer" le lien en code HTML
                switch($typeSocialMedia){
                    case "twitter":
                        $url = 'https://publish.twitter.com/oembed?&align=center&url='.$form->get('lien')->getData();
                        break;

                    case "youtube":
                        $url = 'https://www.youtube.com/oembed?url='.$form->get('lien')->getData().'&format=json';
                        break;
                    
                    case "tiktok":
                        $url = 'https://www.tiktok.com/oembed?url='.$form->get('lien')->getData();
                        break;
                    
                    case "spotify":
                        $url = 'https://open.spotify.com/oembed?url='.$form->get('lien')->getData();
                        break;
                        
                    default:
                        $url = '';
                }

                // Appel l'API avec l'url 
                try{
                    $response = $this->client->request('GET', $url);

                    // Vérifie que l'API nous retourne pas une erreur
                    if($response->getStatusCode() == 200) {
                        $content = $response->toArray();
    
                        $newsfeed = new NewsFeed();
                        $newsfeed->setSocialMedia($typeSocialMedia);
                        $newsfeed->setContentHTML($content['html']);
                        $newsfeed->setContentMessage($contentMessage);
                        $newsfeed->setUser($user);
                        $newsfeed->setDate(new \DateTime());

                        // Créer une chaîne de caractère aléatoire avec une taille de 15 --> bin2hex(random_bytes(15))
                        $newsfeed->setShareLink(bin2hex(random_bytes(15)));
    
                        // Execute la requête SQL
                        $entityManager->persist($newsfeed);
                        $entityManager->flush();
    
                        // Ajoute un coockie sur le navigateur s'il le lien a été ajouté
                        $request->getSession()->set('addURL', true);
    
                        return $this->redirectToRoute('app_news_feed');
                    }
                    else{
                        // Ajoute un coockie sur le navigateur s'il y a eu une erreur
                        $request->getSession()->set('errorAddURL', true);
    
                        return $this->redirectToRoute('app_news_feed');
                    }
                } catch (TransportExceptionInterface $e) {
                    // Ajoute un coockie sur le navigateur s'il y a eu une erreur
                    $request->getSession()->set('errorAddURL', true);
    
                    return $this->redirectToRoute('app_news_feed');
                }
                
            } else {
                // Ajoute un coockie sur le navigateur s'il y a eu une erreur
                $request->getSession()->set('missingChamps', true);

                return $this->render('news_feed/index.html.twig', [
                    'lienShare' => $newsfeedRepo->findBy([], ['id' => 'DESC']),
                    'formNewsFeed' => $form->createView(),
                ]);
            }

        }

        return $this->render('news_feed/index.html.twig', [
            'lienShare' => $newsfeedRepo->findBy([], ['id' => 'DESC']),
            'formNewsFeed' => $form->createView(),
        ]);
    }

    /**
     * Supprime un post
     * 
     * @param string $shareLink
     * @param UserInterface|null $user
     * @param NewsFeed $newsfeed
     * @param NewsFeedRepository $newsfeedRepo
     * @param EntityManagerInterface $entityManager
     * @param NewsFeedLikeRepository $likeRepo
     * @return Response
     */
    #[Route('/delete/{shareLink}', name: 'app_news_feed_delete')]
    public function delete($shareLink, ?UserInterface $user, NewsFeed $newsfeed, NewsFeedRepository $newsfeedRepo, EntityManagerInterface $entityManager, NewsFeedLikeRepository $likeRepo): Response
    {
        // Trouve l'élément à supprimer grâce à son id 
        $newsfeed = $newsfeedRepo->findBy(['shareLink' => $shareLink]);

        // Seul l'utilisateur qui a jouté le post ou un administrateur peut supprimer le post
        if($newsfeed[0]->getUser() == $user || in_array('ROLE_ADMIN', $user->getRoles()))
        {
            // Trouve tous les likes du post sélectionné
            $likesPosts = $likeRepo->findBy([
                'newsfeed' => $newsfeed
            ]);

            // Supprime les likes du post
            foreach($likesPosts as $likePost){
                $entityManager->remove($likePost);
            }
            $entityManager->flush();

            // Supprime l'élément
            $entityManager->remove($newsfeed[0]);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_news_feed');
    }

    /**
     * Ajoute ou supprime un like sur un post
     * 
     * @param string $shareLink
     * @param UserInterface|null $user
     * @param NewsFeed $newsfeed
     * @param EntityManagerInterface $entityManager
     * @param NewsFeedLikeRepository $likeRepo*
     */
    #[Route('/{shareLink}/like', name: 'app_news_feed_like')]
    public function like($shareLink, ?UserInterface $user, NewsFeed $newsfeed, EntityManagerInterface $entityManager, NewsFeedLikeRepository $likeRepo)
    {
        // Retourne une erreur si l'utilisateur n'est pas connecté
        if(!$user)
            return $this->json([
                'code' => 403,
                'message' => "Vous n'êtes pas connecté"
            ], 403);

        // Supprime le like de l'utilisateur sur le poste séléctionné
        if($newsfeed->isLikedByUser($user)) {
            $like = $likeRepo->findOneBy([
                'newsfeed' => $newsfeed,
                'user' => $user
            ]);

            $entityManager->remove($like);
            $entityManager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Like supprimé',
                'likes' => $likeRepo->count(['newsfeed' => $newsfeed])
            ], 200);
        }

        // Ajoute un like sur le poste sélectionné
        $like = new NewsFeedLike();
        $like->setNewsfeed($newsfeed);
        $like->setUser($user);

        $entityManager->persist($like);
        $entityManager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Like ajouté',
            'likes' => $likeRepo->count(['newsfeed' => $newsfeed])
        ], 200);
    }

    /**
     * Affiche le poste grâce au shareLink
     *
     * @param string $shareLink
     * @param NewsFeedRepository $newsfeedRepo
     * @param NewsFeedLikeRepository $likeRepo
     * @return Response
     */
    #[Route('/view/{shareLink}', name: 'app_news_feed_view')]
    public function view($shareLink, ?UserInterface $user, NewsFeedRepository $newsfeedRepo, NewsFeedLikeRepository $likeRepo) : Response
    {
        // Trouve l'élément à supprimer grâce à son shareLink
        $newsfeed = $newsfeedRepo->findBy(['shareLink' => $shareLink]);
        $likes = $likeRepo->findBy([
            'newsfeed' => $newsfeed
        ]);

        return $this->render('news_feed/view.html.twig', [
            'lienShare' => $newsfeed,
            'likes' => $likes, 
        ]);
    }

    /**
     * API - Envoie un mail aux utilisateurs lorsque un élément a été ajouté
     * Celui compte le nombre d'élément mis sur une journée et envoie le nombre d'élément par mail 
     * 
     * N'envoie pas de mail si celui est à 0
     *
     * @param Request $request
     * @param NewsFeedRepository $newsfeedRepo
     * @param UserRepository $userRepository
     * @param MailerInterface $mailer
     * @return Response
     */
    #[Route('/notifier', name: 'app_news_feed_notif', methods : ['POST'])]
    public function notif(Request $request, NewsFeedRepository $newsfeedRepo, UserRepository $userRepository, MailerInterface $mailer): Response
    {
        $userMail = $request->request->get("data");
        if($userMail != ""){
            $findUser = $userRepository->findBy(['email' => $userMail]);

            if($findUser != [] && in_array('ROLE_ADMIN', $findUser[0]->getRoles())) {
                $compteur = 0;
                $userPoste = [];

                // Récupère la date d'aujourd'hui selon la configuration TimeZone de PHP
                $dateActuelle = new \DateTime();
                $dateJourPrecedent = new \DateTime();
                $dateJourPrecedent = $dateJourPrecedent->modify('-1 day');

                // Récupère les dates de tous les postes des utilisateurs
                // Compte le nombre de poste posté dans la journée
                $newsfeed = $newsfeedRepo->findBy([], ['id' => 'DESC']);
                for($i=0; $i<count($newsfeed); $i++){
                    $date[$i] = $newsfeed[$i]->getDate();
                    if($dateActuelle > $date[$i] && $date[$i] > $dateJourPrecedent)
                    {
                        $compteur++;

                        // Ajoute l'utilisateur dans la liste si celui n'est pas déjà présent
                        if(!in_array($newsfeed[$i]->getUser(), $userPoste))
                            $userPoste[$i] = $newsfeed[$i]->getUser();
                    }
                }

                // N'envoie pas de mail si aucun poste n'a été posté à ce jour
                if($compteur != 0)
                {
                    $allUsers = $userRepository->findAll();
                    foreach ($allUsers as $userInfo) {
                        // Envoie le mail si l'utilisateur l'autorise
                        if($userInfo->getReciveMail())
                        {
                            $mailUser = $userInfo->getEmail();
                            $nameUser = $userInfo->getName();
                            $firstnameUser = $userInfo->getfirstname();

                            $email = (new TemplatedEmail())
                            ->from(new Address('no-reply@lavraiemifa.fr', 'La Vraie Mifa'))
                            ->to($mailUser) // $mailUser
                            ->subject("News-Feed - Vous avez manqué ceci !")
                            ->htmlTemplate('news_feed/email.html.twig')
                            ->context([
                                'compteur' => $compteur,
                                'userPoste' => $userPoste,
                                'name' => $nameUser,
                                'firstname' => $firstnameUser
                            ]);

                            $mailer->send($email);
                        }
                    }
                }

                return new JsonResponse([
                    'compteur' => $compteur,
                ], 200);

            }
            else {
                return new JsonResponse([
                    'error' => "Utilisateur non autorisé ou non reconnu",
                ], 403);
            }
        }
        else {
            return new JsonResponse([
                'error' => "Veuillez remplir le champs demandé",
            ], 403);
        }
    }
}
