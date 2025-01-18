<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Podcast;
use App\Repository\PodcastRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PodcastFormType;

#[Route('/podcast')]
class PodcastController extends AbstractController
{
    /**
     * Page principale du podcast
     * @param PodcastRepository $podcastRepository
     */
    #[Route('/', name: 'podcast')]
    public function index(PodcastRepository $podcastRepository): Response
    {
        return $this->render('podcast/index.html.twig', [
            // Récupère tous les podcasts sur le serveur BDD
            'podcasts' => $podcastRepository->findAll()
        ]);
    }

    /**
     * Page pour ajouter un podcast
     * @param Request $request
     * @param PodcastRepository $podcastRepository
     * @param EntityManagerInterface $entityManager
     */
    #[Route('/add', name: 'add_podcast')]
    public function addPodcast(Request $request, PodcastRepository $podcastRepository, EntityManagerInterface $entityManager): Response
    {
        // Créer le formualire pour le podcast
        $podcast = new Podcast();
        $form = $this->createForm(PodcastFormType::class, $podcast);
        $form->handleRequest($request);

        $formulaire = $form->createView();

        // Vérifie l'état du formulaire
        if($form->isSubmitted() && $form->isValid())
        {
            // Stock et récupère le lien pour la BDD
            $podcast->setLienYouTube($form->get('lien')->getData());

            // Execute la requête SQL
            $entityManager->persist($podcast);
            $entityManager->flush($podcast);

            $request->getSession()->set('addPodcast', true);

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('podcast/add.html.twig', [
            'podcastForm' => $formulaire
        ]); 
    }

    /**
     * Supprime un podcast
     * @param int $id
     * @param PodcatsRepository $podcastRepository
     * @param EntityManagerInterface $entityManager
     */
    #[Route('/delete/{id}', name: 'delete_podcast')]
    public function deletePodcast($id, PodcastRepository $podcastRepository, EntityManagerInterface $entityManager): Response
    {
        // Trouve le podcast en fonction de l'id
        $podcast = $podcastRepository->find($id);

        // Supprime le podcast
        $entityManager->remove($podcast);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin');
    }

    /**
     * Affiche la liste des liens
     * @param PodcastRepository $podcastRepository
     */
    #[Route('/show', name: 'show_podcast')]
    public function showPodcast(PodcastRepository $podcastRepository): Response
    {
        return $this->render('podcast/show.html.twig', [
            // Récupère tous les liens du podcast
            'podcasts' => $podcastRepository->findAll()
        ]);
    }
}
