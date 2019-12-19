<?php

namespace App\Controller;

use App\Entity\Game;
use App\Service\PlayService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayController extends AbstractController
{
    private $playService;

    public function __construct(PlayService $playService)
    {
        $this->playService = $playService;
    }

    /**
     * @Route("/play/{id}", name="play", methods={"GET"})
     * @isGranted("ROLE_USER")
     *
     * @return Response
     */
    public function gamePlay(Game $game)
    {
        $this_player = null;
        $playing = null;
        $_players = $game->getPlayers();

        foreach ($_players as $player) {
            if ($player->getIsAlive()) {
                $playersByOrder[$player->getTurn()] = $player;
            }
            if ($this->getUser()->getId() == $player->getUser()->getId()) {
                $idPlayerSession = $player->getId();
            }
            if ($player->getIsPlaying()) {
                $playing = $player;
            }
        }

        ksort($playersByOrder, SORT_NUMERIC);

        $this->get('session')->set('idGame', $game->getId());
        $this->get('session')->set('idPlayer', $idPlayerSession);

        $data_logs = $this->playService->getNewLogs(0, $game->getId());
        $_logs = $data_logs['logs'];
        $lastIdLog = $data_logs['lastLog'];

        $this->playService->setLastLog($idPlayerSession, $lastIdLog);



        if ($this->playService->hasActionToDo($idPlayerSession)) {
            $playDiv = $this->playService->displayAction('active', $idPlayerSession, false);
            $hasAction = 1;
        } else {
            $hasAction = 0;
            $log = $this->playService->findLog($lastIdLog);

            $idPlayerActive = $log->getPlayer()->getId();
            $playDiv = $this->playService->displayAction('passive', $idPlayerActive, false);
        }

        return $this->render('play/index.html.twig', [
            'game' => $game,
            'players_by_order' => $playersByOrder,
            'playing' => $playing,
            'list_logs' => $_logs,
            'playDiv' => $playDiv,
            'hasAction' => $hasAction,
        ]);
    }

    /**
     * @Route("/play/hasActionToDo", name="play.hasActionToDo", methods={"POST"})
     * @isGranted("ROLE_USER")
     *
     * @return Response
     */
    public function hasActionToDo()
    {
        $idPlayerSession = $this->get('session')->get('idPlayer');
        $hasActionToDo = $this->playService->hasActionToDo($idPlayerSession);

        return new JsonResponse(json_encode($hasActionToDo));
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
            $log = $this->playService->findLog($idLog);
            $idPlayerActive = $log->getPlayer()->getId();
        }

        $_return = $this->playService->displayAction($mode, $idPlayerActive, true);

        return new JsonResponse(json_encode($_return));
    }

    /**
     * @Route("/play/validAction", name="play.validAction", methods={"POST"})
     * @isGranted("ROLE_USER")
     *
     * @return Response
     */
    public function validAction()
    {
        $idPlayerSession = $this->get('session')->get('idPlayer');

        return new JsonResponse(json_encode($this->playService->validAction($idPlayerSession)));
    }

    /**
     * @Route("/play/updateLastSeenLog", name="play.updateLastSeenLog", methods={"POST"})
     * @isGranted("ROLE_USER")
     *
     * @return Response
     */
    public function updateLastSeenLog(Request $req)
    {
        $idLog = $req->get('idLog');
        $idPlayer = $this->get('session')->get('idPlayer');
        $log = $this->playService->findLog($idLog);
        $this->playService->setLastLog($idPlayer, $log);

        return new JsonResponse(json_encode(true));
    }

    /**
     * @Route("/play/getNewLogs", name="play.getNewLogs", methods={"POST"})
     * @isGranted("ROLE_USER")
     *
     * @return Response
     */
    public function getNewLogs()
    {
        $idPlayer = $this->get('session')->get('idPlayer');
        $idGame = $this->get('session')->get('idGame');

        $lastLog = $this->playService->getLastLog($idPlayer);
        $data_logs = $this->playService->getNewLogs($lastLog, $idGame);

        return new JsonResponse(json_encode($data_logs['logs']));
    }
}
