<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;

#[Route('/java')]
class JavaController extends AbstractController
{
    #[Route('/', name: 'app_java',  methods: ['GET'])]
    public function index()
    {
        return $this->render('java/index.html.twig', [
            'controller_name' => 'JavaController',
        ]);
    }

    #[Route('/lecteurs', name: 'app_java_lecteur',  methods: ['GET'])]
    public function showLecteurs()
    {
        $bdd = new \PDO('mysql:host=localhost;dbname=biblio;charset=utf8', 'java', 'fhypbHTQuu9nGxzL');
        $sql =  'SELECT * FROM Utilisateur';

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

    #[Route('/lecteurs/add', name: 'app_java_add_lecteur',  methods: ['POST'])]
    public function addLecteurs(Request $request)
    {
        $bdd = new \PDO('mysql:host=localhost;dbname=biblio;charset=utf8', 'java', 'fhypbHTQuu9nGxzL');

        $sql =  'INSERT INTO Utilisateur (Nom, Prenom) VALUES (?,?)';

        $sth = $bdd->prepare($sql);
        $result = $sth->execute(array($request->get('Nom'), $request->get('Prenom')));

        return new JsonResponse([
            'Fait' => $result
        ]);
    }

    #[Route('/livres', name: 'app_java_livre',  methods: ['GET'])]
    public function showLivres()
    {
        $bdd = new \PDO('mysql:host=localhost;dbname=biblio;charset=utf8', 'java', 'fhypbHTQuu9nGxzL');
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

    #[Route('/livres/add', name: 'app_java_add_livres',  methods: ['POST'])]
    public function addLivre(Request $request)
    {
        $bdd = new \PDO('mysql:host=localhost;dbname=biblio;charset=utf8', 'java', 'fhypbHTQuu9nGxzL');

        $sql =  'INSERT INTO Livres (Auteur, Titre) VALUES (?,?)';

        $sth = $bdd->prepare($sql);
        $result = $sth->execute(array($request->get('Auteur'), $request->get('Titre')));

        return new JsonResponse([
            'Fait' => $result
        ]);
    }
}
