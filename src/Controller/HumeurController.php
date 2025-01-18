<?php


namespace App\Controller;

use App\Entity\Motivation;
use App\Repository\MotivationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/humeur")
 */
class HumeurController extends AbstractController
{
    /**
     * @Route("/today", name="humeur_today_index")
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('maintenance.html.twig', []);
        /*return $this->render('humeur/index.html.twig', [
            'users' => $userRepository->getAllMotivation(date('Y-m-d'), date('W')),
        ]);*/
    }

    /**
     * @Route("/resultats", name="humeur_resultats")
     * @Route("/resultats/{date}", name="humeur_resultats_with_date")
     */
    public function result(UserRepository $userRepository, MotivationRepository $motivationRepository, string $date = null): Response
    {
        if ($date) {
            $dateFormat = new \DateTime($date);
            $numSemaine = $dateFormat->format('W');
        } else {
            $date = date('Y-m-d');
            $numSemaine = date('W');
        }

        return $this->render('humeur/resultats.html.twig', [
            'date' => $date,
            'users' => $userRepository->getAllMotivation($date, $numSemaine),
            'moyJour' => $motivationRepository->getMoyJour($date, $numSemaine),
            'moySemaine' => $motivationRepository->getMoySemaine($numSemaine),
        ]);
    }

    /**
     * @Route("/save/{value}", name="humeur_save", methods={"POST"}, requirements={"value"="\d+"})
     */
    public function saveValueHumeur(int $value, EntityManagerInterface $em, MotivationRepository $motivationRepository, Request $request): Response
    {
        $idMotivation = $request->request->get('id');
        if ($idMotivation) {
            $motivation = $motivationRepository->find($idMotivation);
            $motivation
                ->setVlMotivation($value)
                ->setAbsence(false)
                ->setDtSaisie(new \DateTime());
        } else {
            $motivation = (new Motivation())
                ->setUser($this->getUser())
                ->setVlMotivation($value)
                ->setNumSemaine(date('W'))
                ->setDtSaisie(new \DateTime())
                ->setAbsence(false);
        }

        $em->persist($motivation);
        $em->flush();

        return $this->json([
            'id' => $motivation->getId(),
        ]);
    }

    /**
     * @Route("/save/commentaire", name="comment_save", methods={"POST"})
     */
    public function saveComment(Request $request, EntityManagerInterface $em, MotivationRepository $motivationRepository): Response
    {
        $idMotivation = $request->request->get('id');
        $commentaire = $request->request->get('commentaire');

        if ($idMotivation && $commentaire) {
            $motivation = $motivationRepository->find($idMotivation);
            if ($motivation) {
                $motivation->setCommentaire($commentaire);

                $em->persist($motivation);
                $em->flush();

                return $this->json(true);
            } else {
                throw new NotFoundHttpException('Params not found');
            }
        } else {
            throw new NotFoundHttpException('Params not found');
        }
    }

    /**
     * @Route("/save/absence", name="absence_save", methods={"POST"})
     */
    public function saveAbsence(Request $request, EntityManagerInterface $em, UserRepository $userRepository, MotivationRepository $motivationRepository): Response
    {
        $idUser = $request->request->get('idUser');
        $flagAbs = $request->request->get('abs');
        $user = $userRepository->find($idUser);
        $idMotivation = $request->request->get('idMotivation');
        $checkMotivation = $userRepository->getUserMotivation(date('Y-m-d'), date('W'), $idUser);

        if ($user && !empty($flagAbs)) {
            if (!$checkMotivation) {
                if ($idMotivation) {
                    $motivation = $motivationRepository->find($idMotivation);
                    $motivation
                        ->setAbsence($flagAbs)
                        ->setDtSaisie(new \DateTime());
                } else {
                    $motivation = (new Motivation())
                        ->setAbsence($flagAbs)
                        ->setUser($user)
                        ->setNumSemaine(date('W'))
                        ->setDtSaisie(new \DateTime());
                }
                $em->persist($motivation);
                $em->flush();

                $id = $motivation->getId();
            } else {
                $id = $checkMotivation['idMotivation'];
            }

            return $this->json([
                'id' => $id,
            ]);

        } else {
            throw new NotFoundHttpException('Params not found');
        }
    }
}
