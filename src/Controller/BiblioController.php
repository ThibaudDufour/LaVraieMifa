<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[Route('/biblio', name: 'app_biblio')]
class BiblioController extends AbstractController
{
    #[Route('/', name: 'app_biblio')]
    public function index(): Response
    {
        return $this->render('biblio/index.html.twig', [
            'controller_name' => 'BiblioController',
        ]);
    }

    #[Route('/showLecteurs', name: 'app_biblio_lecteur',  methods: ['GET'])]
    public function showLecteurs()
    {
        $bdd = new \PDO('mysql:host=localhost;dbname=biblio2;charset=utf8', 'java2', '4UYRLq0myZ7UtRlX');
        $sql =  'SELECT * FROM lecteur';

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

    #[Route('/newLecteur', name: 'app_biblio_new_lecteur',  methods: ['POST'])]
    public function addLecteurs(Request $request)
    {
        $bdd = new \PDO('mysql:host=localhost;dbname=biblio2;charset=utf8', 'java2', '4UYRLq0myZ7UtRlX');

        $sql =  'INSERT INTO lecteur (nom, prenom, email) VALUES (?,?,?)';

        $sth = $bdd->prepare($sql);
        $result = $sth->execute(array($request->get('nom'), $request->get('prenom'), $request->get('email')));

        return new JsonResponse([
            'Fait' => $result
        ]);
    }

    #[Route('/deleteLecteur', name: 'app_biblio_delete_lecteur',  methods: ['POST'])]
    public function deleteLecteurs(Request $request)
    {
        $bdd = new \PDO('mysql:host=localhost;dbname=biblio2;charset=utf8', 'java2', '4UYRLq0myZ7UtRlX');

        $sql =  'DELETE FROM lecteur WHERE id=?';

        $sth = $bdd->prepare($sql);
        $result = $sth->execute(array($request->get('id')));

        return new JsonResponse([
            'Fait' => $result
        ]);
    }

    #[Route('/showLivres', name: 'app_biblio_livre',  methods: ['GET'])]
    public function showLivres()
    {
        $bdd = new \PDO('mysql:host=localhost;dbname=biblio2;charset=utf8', 'java2', '4UYRLq0myZ7UtRlX');
        $sql =  'SELECT * FROM livre';

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

    #[Route('/newLivre', name: 'app_biblio_new_livre',  methods: ['POST'])]
    public function addLivre(Request $request)
    {
        $bdd = new \PDO('mysql:host=localhost;dbname=biblio2;charset=utf8', 'java2', '4UYRLq0myZ7UtRlX');

        $sql =  'INSERT INTO livre (titre, auteur, annee) VALUES (?,?,?)';

        $sth = $bdd->prepare($sql);
        $result = $sth->execute(array($request->get('titre'), $request->get('auteur'), $request->get('annee')));

        return new JsonResponse([
            'Fait' => $result
        ]);
    }

    #[Route('/deleteLivre', name: 'app_biblio_delete_livre',  methods: ['POST'])]
    public function deleteLivre(Request $request)
    {
        $bdd = new \PDO('mysql:host=localhost;dbname=biblio2;charset=utf8', 'java2', '4UYRLq0myZ7UtRlX');

        $sql =  'DELETE FROM livre WHERE id=?';

        $sth = $bdd->prepare($sql);
        $result = $sth->execute(array($request->get('id')));

        return new JsonResponse([
            'Fait' => $result
        ]);
    }
}
