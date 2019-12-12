<?php

namespace App\Controller;

use App\Entity\Game;
use App\Service\PlayService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayController extends AbstractController
{
    /**
     * @Route("/play/{id}", name="play", methods={"GET"})
     * @isGranted("ROLE_USER")
     *
     * @return Response
     */
    public function gamePlay(Game $game)
    {
        $this_player = null;
        $_players = $game->getPlayers();

        foreach ($_players as $player) {
            $playersByOrder[$player->getTurn()] = $player;
            if ($this->getUser()->getId() == $player->getUser()->getId()) {
                $this_player = $player->getId();
            }
        }

        ksort($playersByOrder, SORT_NUMERIC);

        return $this->render('play/index.html.twig', [
            'game' => $game,
            'players_by_order' => $playersByOrder,
            'this_player' => $this_player,
        ]);
    }
}
