<?php

namespace App\Controller;

use App\Entity\Game;
use App\Service\GameDisplayService;
use App\Service\GamePlayService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameDisplayController extends AbstractController
{
    private $gamePlayService;
    private $gameDisplayService;

    public function __construct(GamePlayService $gamePlayService, GameDisplayService $gameDisplayService)
    {
        $this->gamePlayService = $gamePlayService;
        $this->gameDisplayService = $gameDisplayService;
    }

    /**
     * @Route("/play/{id}", name="play", methods={"GET"})
     * @isGranted("ROLE_USER")
     *
     * @return Response
     */
    public function gamePlay(Game $game)
    {
        $_players = $game->getPlayers();
        foreach ($_players as $player) {
            $playersByOrder[$player->getTurn()] = $player;

            if ($this->getUser()->getId() == $player->getUser()->getId()) {
                $idPlayerSession = $player->getId();
            }
            if ($player->getIsPlaying()) {
                $playerPlaying = $player;
            }
        }

        ksort($playersByOrder, SORT_NUMERIC);

        $this->get('session')->set('idPlayer', $idPlayerSession);

        $_dataLogs = $this->gamePlayService->getAllLogs($idPlayerSession);
        $idLastLogSeen = (null !== $_dataLogs) ? max(array_keys($_dataLogs)) : 0;
        $this->gamePlayService->setLastLog($idPlayerSession, $idLastLogSeen);

        $playDiv = $this->gameDisplayService->displayActionToDo($idPlayerSession);

        if ($this->gamePlayService->hasActionToDo($idPlayerSession)) {
            $hasAction = 1;
            $nameAction = $playDiv['nameAction'];
        } else {
            $hasAction = 0;
            $log = $this->gamePlayService->findLog($idLastLogSeen);
            if (null !== $log) {
                $nameAction = $playDiv['nameAction'];
            } else {
                $playDiv = ['playBody' => 'En attente de '.$playerPlaying->getUser()->getUsername(), 'playBtn' => null, 'playBtn2' => null];
                $nameAction = 'start_turn';
            }
        }

        $_cards = $game->getKotCards();
        foreach ($_cards as $card) {
            switch ($card->getState()) {
                case 'achat':
                    $cards['achat'][$card->getPosition()] = $card;
                    break;
                case 'player':
                    $cards['player'][$card->getPlayer()->getId()][] = $card;
                    break;
            }
        }

        return $this->render('play/index.html.twig', [
            'game' => $game,
            'players_by_order' => $playersByOrder,
            'playing' => $playerPlaying,
            'list_logs' => $_dataLogs,
            'playDiv' => $playDiv,
            'hasAction' => $hasAction,
            'nameAction' => $nameAction,
            'cards' => $cards,
        ]);
    }

    /**
     * @Route("/play/displayActionToDo", name="play.displayActionToDo", methods={"POST"})
     * @isGranted("ROLE_USER")
     *
     * @return Response
     */
    public function displayActionToDo(Request $req)
    {
        $mode = $req->get('mode');
        if ('active' == $mode) {
            $idPlayerActive = $this->get('session')->get('idPlayer');
        } else {
            $idLog = $req->get('idLog');
            $log = $this->gamePlayService->findLog($idLog);
            $idPlayerActive = $log->getPlayer()->getId();
        }

        $_return = $this->gamePlayService->displayAction($mode, $idPlayerActive);

        return new JsonResponse(json_encode($_return));
    }
}
