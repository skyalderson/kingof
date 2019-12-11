<?php

namespace App\Controller;

use App\Entity\Game;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PlayController extends AbstractController
{
    /**
     * @Route("/play/{id}", name="play", methods={"GET"})
     * @isGranted("ROLE_USER")
     *
     * @param Game $game
     * @return Response
     */
    public function gameQuit(Game $game)
    {
        //$this->denyAccessUnlessGranted('play', $game);

        return $this->render('play/index.html.twig', [
            'game' => $game,
        ]);
    }
}
