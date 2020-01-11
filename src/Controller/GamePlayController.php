<?php

namespace App\Controller;

use App\Service\GameDisplayService;
use App\Service\GamePlayService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GamePlayController extends AbstractController
{
    private $gamePlayService;
    private $gameDisplayService;

    public function __construct(GamePlayService $gamePlayService, GameDisplayService $gameDisplayService)
    {
        $this->gamePlayService = $gamePlayService;
        $this->gameDisplayService = $gameDisplayService;
    }

    /**
     * @Route("/play/getNewData", name="play.getNewData", methods={"POST"})
     * @isGranted("ROLE_USER")
     *
     * @return Response
     */
    public function getNewData(Request $req)
    {
        if ($req->isXmlHttpRequest()) {
            $idPlayerSession = $this->get('session')->get('idPlayer');

            $_dataLogs = $this->gamePlayService->getNewLogs($idPlayerSession);
            $_dataAction = $this->gameDisplayService->displayActionToDo($idPlayerSession);
            $_dataGameData = $this->gamePlayService->getNewGameDataUpdates($_dataLogs, $idPlayerSession);
            $hasActionToDo = $this->gamePlayService->hasActionToDo($idPlayerSession);

            return new JsonResponse(json_encode(['logs' => $_dataLogs, 'action' => $_dataAction, 'gameData' => $_dataGameData, 'hasActionToDo' => $hasActionToDo]));
        } else {
            return new Response('ERROR', 400);
        }
    }

    /**
     * @Route("/play/updateLastSeenLog", name="play.updateLastSeenLog", methods={"POST"})
     * @isGranted("ROLE_USER")
     *
     * @return Response
     */
    public function updateLastSeenLog(Request $req)
    {
        if ($req->isXmlHttpRequest()) {
            $idLog = $req->get('idLog');
            $idPlayer = $this->get('session')->get('idPlayer');
            $this->gamePlayService->setLastLog($idPlayer, $idLog);

            return new JsonResponse(json_encode(true));
        } else {
            return new Response('ERROR', 400);
        }
    }

    /**
     * @Route("/play/validAction", name="play.validAction", methods={"POST"})
     * @isGranted("ROLE_USER")
     *
     * @return Response
     */
    public function validAction(Request $req)
    {
        if ($req->isXmlHttpRequest()) {
            $idPlayerSession = $this->get('session')->get('idPlayer');
            $data = $req->get('data');

            $this->gamePlayService->validAction($idPlayerSession, $data);

            $_dataLogs = $this->gamePlayService->getNewLogs($idPlayerSession);
            $_dataAction = $this->gameDisplayService->displayActionToDo($idPlayerSession);
            $_dataGameData = $this->gamePlayService->getNewGameDataUpdates($_dataLogs, $idPlayerSession);
            $hasActionToDo = $this->gamePlayService->hasActionToDo($idPlayerSession);

            return new JsonResponse(json_encode(['logs' => $_dataLogs, 'action' => $_dataAction, 'gameData' => $_dataGameData, 'hasActionToDo' => $hasActionToDo]));
        } else {
            return new Response('ERROR', 400);
        }
    }
}
