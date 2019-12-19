<?php

namespace App\Service;

use App\Entity\Log;
use App\Repository\GameRepository;
use App\Repository\LogRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;

class PlayService
{
    private $em;

    private $playerRepo;
    private $logRepo;
    private $gameRepo;

    public function __construct(PlayerRepository $playerRepo, LogRepository $logRepo, GameRepository $gameRepo, EntityManagerInterface $em)
    {
        $this->em = $em;

        $this->playerRepo = $playerRepo;
        $this->logRepo = $logRepo;
        $this->gameRepo = $gameRepo;
    }

    // Teste si le joueur de la session est celui ayant une action de jeu à effectuer
    public function hasActionToDo($idPlayerSession)
    {
        $_logToDo = $this->getLogActionTodo($idPlayerSession);
        if (0 !== count($_logToDo)) {
            $logToDo = $_logToDo[0];
            $idPlayerToPlay = $logToDo->getPlayer()->getId();

            return $idPlayerSession == $idPlayerToPlay;
        } else {
            return false;
        }
    }

    public function displayAction($mode, $idPlayerActive, $execAction)
    {
        $_logToDo = $this->getLogActionTodo($idPlayerActive);
        $player = $this->playerRepo->find($idPlayerActive);

        if (0 !== count($_logToDo)) {
            $logToDo = $_logToDo[0];

            $nameAction = $logToDo->getAction();

            switch ($nameAction) {
                case 'start_turn':
                    if ('active' === $mode) {
                        $playBody = "C'est à toi de jouer !";
                        $playBtn = 'OK';
                    } else {
                        $playBody = 'En attente de '.$player->getUser()->getUsername();
                        $playBtn = null;
                    }
                    break;

                case 'is_starting_in_city':
                    if ('active' === $mode) {
                        if ($this->isInCity($idPlayerActive)) {
                            if ($execAction) {
                                $victoryPoints = $this->addVictoryPointsIfInCity($idPlayerActive);
                            } else {
                                $victoryPoints = $this->getVictoryPointsIfInTokyo($idPlayerActive);
                            }
                            $playBody = "Tu commences ton tour dans Tokyo et gagnes $victoryPoints points de victoire";
                        } else {
                            $playBody = 'Tu es hors de Tokyo et ne gagnes pas de points de victoire';
                        }
                        $playBtn = 'OK';
                    } else {
                        if ($this->isInCity($idPlayerActive)) {
                            $victoryPoints = $this->getVictoryPointsIfInTokyo($idPlayerActive);
                            $playBody = $player->getUser()->getUsername()." commence son tour dans Tokyo et gagne $victoryPoints points de victoire";
                        } else {
                            $playBody = $player->getUser()->getUsername().' est hors de Tokyo et ne gagne pas de points de victoire';
                        }
                        $playBtn = null;
                    }
                    break;

                case 'init_dices':
                    $playBody = $this->displayDicesInit($player->getNbDices());
                    if ('active' === $mode) $playBtn = 'OK';
                    else $playBtn = null;
                    break;

                case 'throw_dices':

                    $playBody = $this->displayDicesThrow($player->getNbDices());
                    if ('active' === $mode) $playBtn = 'OK';
                    else $playBtn = null;
                    break;

                case 'end_turn':
                    if ('active' === $mode) {
                        $playBody = 'Tu as fini ton tour';
                        $playBtn = 'OK';
                    } else {
                        $playBody = $player->getUser()->getUsername().' a fini son tour';
                        $playBtn = null;
                    }
                    break;
            }
        } else {
            $playBody = '';
            $playBtn = null;
        }

        return ['playBody' => $playBody, 'playBtn' => $playBtn];
    }

    // récupère les infos de l'action à accomplir
    public function getLogActionTodo($idPlayerSession)
    {
        $playerSession = $this->playerRepo->find($idPlayerSession);
        $idGame = $playerSession->getGame()->getId();

        return $this->logRepo->findLastLogToDoByGame($idGame, $idPlayerSession);
    }

    public function validAction($idPlayerSession)
    {
        $_logDone = $this->getLogActionTodo($idPlayerSession);
        $logDone = $_logDone[0];
        $nameAction = $logDone->getAction();
        $playerAction = $logDone->getPlayer();
        $game = $playerAction->getGame();

        $_return['idLog'] = $logDone->getId();

        $logDone->setIsDone(true);

        $nextLog = new Log();
        $nextLog->setIsDone(false);
        $nextLog->setGame($game);
        $nextLog->setPlayer($playerAction);

        switch ($nameAction) {
            case 'start_turn':
                $nextLog->setAction('is_starting_in_city');
                break;

            case 'is_starting_in_city':
                $nextLog->setAction('init_dices');
                break;

            case 'init_dices':
                $nextLog->setAction('throw_dices');
                break;

            case 'throw_dices':
                $nextLog->setAction('end_turn');
                break;

            case 'end_turn':
                $nextPlayer = $this->findNextPlayer($game, $playerAction);

                $nextLog->setPlayer($nextPlayer);
                $playerAction->setIsPlaying(false);
                $nextPlayer->setIsPlaying(true);
                $nextLog->setAction('start_turn');
                $this->em->persist($playerAction);

                $_return['isplaying_name'] = $nextPlayer->getUser()->getUsername();
                $_return['isplaying_id'] = $nextPlayer->getId();
                $_return['playBody'] = "<div class='col-xl-12' style='font-size:200%'>Ton tour est terminé</div>";
                break;
        }

        $_return['action'] = $nameAction;

        $this->em->persist($logDone);
        $this->em->persist($playerAction);
        $this->em->persist($nextLog);
        $this->em->flush();

        return $_return;
    }

    public function findNextPlayer($game, $playerAction)
    {
        $_players = $game->getPlayers();

        foreach ($_players as $player) {
            if ($player->getIsAlive()) {
                $playersByOrder[$player->getTurn()] = $player;
            }
        }
        ksort($playersByOrder, SORT_NUMERIC);

        $currentIsFound = false;
        $nextPlayer = null;
        foreach ($playersByOrder as $player) {
            if ($player === $playerAction) {
                $currentIsFound = true;
                continue;
            }

            if ($currentIsFound) {
                $nextPlayer = $player;
                break;
            }
        }
        if (null === $nextPlayer) {
            foreach ($playersByOrder as $player) {
                $nextPlayer = $player;
                break;
            }
        }

        return $nextPlayer;
    }

    public function getNewLogs($lastLog, $idGame)
    {
        $_newLogs = $this->logRepo->getNewLogs($lastLog, $idGame);

        if (0 !== count($_newLogs)) {
            foreach ($_newLogs as $v) {
                $player = $this->playerRepo->find($v['idPlayer']);

                $_data['namePlayer'] = $player->getUser()->getUsername();

                // On passe les données spécifiques à chaque action à la fonction d'affichage du log
                switch ($v['action']) {
                    case 'is_starting_in_city':
                       $_data['inCity'] = $player->getInCity();
                       $_data['idPlayer'] = $v['idPlayer'];
                       break;
                }

                $_return['logs'][$v['idLog']]['idLog'] = $v['idLog'];
                $_return['logs'][$v['idLog']]['action'] = $v['action'];
                $_return['logs'][$v['idLog']]['wasplaying_id'] = $v['idPlayer'];
                $_return['logs'][$v['idLog']]['wasplaying_name'] = $player->getUser()->getUsername();
                $_return['logs'][$v['idLog']]['htmlContentLog'] = $this->getHTMLContentLog($v['action'], $_data);
                $_return['logs'][$v['idLog']]['bgClassLog'] = 'bg-purple';

                $lastIdLog = $v['idLog'];
                $_return['lastLog'] = $lastIdLog;
                $lastNameAction = $v['action'];
            }

            switch ($lastNameAction) {
                case 'end_turn':
                    $game = $this->gameRepo->find($idGame);
                    $nextPlayer = $this->findNextPlayer($game, $player);

                    $_return['logs'][$lastIdLog]['isplaying_name'] = $nextPlayer->getUser()->getUsername();
                    $_return['logs'][$lastIdLog]['isplaying_id'] = $nextPlayer->getId();

                    //$_return['logs'][$lastIdLog]['playBody'] = "<div class='col-xl-12' style='font-size:200%'>Le tour de ".$_return['logs'][$lastIdLog]['wasplaying_name'].' est terminé</div>';

                    break;
            }

            // RECUP DONNEES LAST ACTION !!!!

            return $_return;
        } else {
            return null;
        }
    }

    public function setLastLog($idPlayer, $idLog)
    {
        $player = $this->playerRepo->find($idPlayer);
        if (null !== $idLog) {
            $log = $this->logRepo->find($idLog);
            $player->setLastLog($log);
        } else {
            $player->setLastLog(null);
        }
        $this->em->persist($player);
        $this->em->flush();
    }

    public function getLastLog($idPlayer)
    {
        $player = $this->playerRepo->find($idPlayer);

        if (null === $player->getLastLog()) {
            return 0;
        } else {
            return $player->getLastLog()->getId();
        }
    }

    public function findLog($idLog)
    {
        return $this->logRepo->find($idLog);
    }

    public function getHTMLContentLog($action, $_data)
    {
        switch ($action) {
            case 'start_turn':
                return 'Le tour de '.$_data['namePlayer'].' commence.';
                break;

            case 'is_starting_in_city':
                if (0 === $_data['inCity']) {
                    return $_data['namePlayer'].' commence son tour hors de Tokyo et ne gagne pas de points de victoire.';
                } else {
                    return $_data['namePlayer'].' commence son tour dans Tokyo et gagne '.$this->getVictoryPointsIfInTokyo($_data['idPlayer']).' points de victoire.';
                }
                break;

            case 'init_dices':
                return $_data['namePlayer'].' lance les dés';
                break;

            case 'throw_dices':
                return $_data['namePlayer'].' lance encore les dés';
                break;

            case 'end_turn':
                return $_data['namePlayer'].' a terminé son tour.';
                break;
        }
    }

    public function isInCity($idPlayer)
    {
        $player = $this->playerRepo->find($idPlayer);

        return 0 !== $player->getInCity();
    }

    public function addVictoryPointsIfInCity($idPlayer)
    {
        $pointsWon = $this->getVictoryPointsIfInTokyo($idPlayer);

        $player = $this->playerRepo->find($idPlayer);
        $player->setGp($player->getGp() + $pointsWon);

        $this->em->persist($player);
        $this->em->flush();

        return $pointsWon;
    }

    public function getVictoryPointsIfInTokyo($idPlayer)
    {
        return 2;
    }

    public function displayDicesThrow($nb_dices)
    {
        $html = "<div class='row'>";
        for ($i = 1; $i <= $nb_dices; ++$i) {
            $diceColor = ($i <= 6) ? 'black' : 'green';

            $html .= "<div class='col m-0 p-1'><img class='img-fluid' src='/img/dices/$diceColor/dice.gif'></div>";
        }
        $html .= '</div>';

        return $html;
    }

    public function displayDicesInit($nb_dices)
    {
        $html = "<div class='row'>";
        for ($i = 1; $i <= $nb_dices; ++$i) {
            $diceColor = ($i <= 6) ? 'black' : 'green';

            switch ($i) {
                case 1:
                    $faceDice = 'one';
                    break;
                case 2:
                    $faceDice = 'two';
                    break;
                case 3:
                    $faceDice = 'three';
                    break;
                case 4:
                case 7:
                    $faceDice = 'baffe';
                    break;
                case 5:
                case 8:
                    $faceDice = 'heart';
                    break;
                case 6:
                    $faceDice = 'flash';
                    break;
            }

            $html .= "<div class='col m-0 p-1'><img class='img-fluid' src='/img/dices/$diceColor/dice_$faceDice.png'></div>";
        }

        $html .= '</div>';

        return $html;
    }
}
