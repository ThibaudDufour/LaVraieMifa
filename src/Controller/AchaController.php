<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/acha')]
class AchaController extends AbstractController
{
    #[Route('/', name: 'app_acha')]
    public function index(): Response
    {
        return $this->render('acha/index.html.twig', [
        ]);
    }

    #[Route('/get/Livres', name: 'app_acha_livres',  methods: ['GET'])]
    public function showLivres()
    {
        $bdd = new \PDO('mysql:host=localhost;dbname=acha_gmon;charset=utf8', 'java3', 'GkAL5Vf5RtXRTDRy');
        $sql =  'SELECT * FROM Livres';

        $result = false;

        $sth = $bdd->prepare($sql);
        $sth->execute();

        $i = 0;
        while ($row = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $result[$i] = $row;
            $i++;
        }

        return new JsonResponse(
            $result
        );
    }

    #[Route('/get/Lecteurs', name: 'app_acha_lecteurs',  methods: ['GET'])]
    public function showLecteurs()
    {
        $bdd = new \PDO('mysql:host=localhost;dbname=acha_gmon;charset=utf8', 'java3', 'GkAL5Vf5RtXRTDRy');
        $sql =  'SELECT * FROM Lecteurs';

        $result = false;

        $sth = $bdd->prepare($sql);
        $sth->execute();

        $i = 0;
        while ($row = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $result[$i] = $row;
            $i++;
        }

        return new JsonResponse(
            $result
        );
    }

    #[Route('/set/Livres', name: 'app_acha_add_livre',  methods: ['POST'])]
    public function addLivre(Request $request)
    {
        $bdd = new \PDO('mysql:host=localhost;dbname=acha_gmon;charset=utf8', 'java3', 'GkAL5Vf5RtXRTDRy');

        $sql =  'INSERT INTO Livres (titre, auteur) VALUES (?,?)';

        $sth = $bdd->prepare($sql);
        $result = $sth->execute(array($request->get('titre'), $request->get('auteur')));

        return new JsonResponse([
            'Fait' => $result
        ]);
    }

    #[Route('/set/Lecteurs', name: 'app_acha_add_lecteur',  methods: ['POST'])]
    public function addLecteurs(Request $request)
    {
        $bdd = new \PDO('mysql:host=localhost;dbname=acha_gmon;charset=utf8', 'java3', 'GkAL5Vf5RtXRTDRy');

        $sql =  'INSERT INTO Lecteurs (nom, prenom, email) VALUES (?,?,?)';

        $sth = $bdd->prepare($sql);
        $result = $sth->execute(array($request->get('nom'), $request->get('prenom'), $request->get('email')));

        return new JsonResponse([
            'Fait' => $result
        ]);
    }
}
