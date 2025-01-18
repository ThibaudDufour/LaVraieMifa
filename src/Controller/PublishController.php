<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\Publisher;

class PublishController extends AbstractController
{
    /**
     * @Route("/ping", name="ping", methods={"POST"})
     * @param Publisher $publisher
     * @return Response
     */
    public function __invoke(HubInterface $hub)
    {
        $update = new Update(
            'newsfeed',
            json_encode(['status' => true])
        );
 
        // The Publisher service is an invokable object
        $hub->publish($update);
 
        return $this->redirectToRoute('home');
    }
}
