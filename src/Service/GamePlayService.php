<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\GameDataUpdate;
use App\Entity\Log;
use App\Entity\Player;
use App\Entity\ResolveOrder;
use App\Entity\ThrowTokyoDice;
use App\Repository\GameDataUpdateRepository;
use App\Repository\GameRepository;
use App\Repository\LogRepository;
use App\Repository\PlayerRepository;
use App\Repository\ThrowTokyoDiceRepository;
use Doctrine\ORM\EntityManagerInterface;

class GamePlayService
{
    private $em;

    private $playerRepo;
    private $logRepo;
    private $gameRepo;
    private $throwTokyoDiceRepo;
    private $gameDataUpdateRepo;

    public function __construct(PlayerRepository $playerRepo, LogRepository $logRepo, GameRepository $gameRepo, ThrowTokyoDiceRepository $throwTokyoDiceRepo, GameDataUpdateRepository $gameDataUpdateRepo, EntityManagerInterface $em)
    {
        $this->em = $em;

        $this->playerRepo = $playerRepo;
        $this->logRepo = $logRepo;
        $this->gameRepo = $gameRepo;
        $this->throwTokyoDiceRepo = $throwTokyoDiceRepo;
        $this->gameDataUpdateRepo = $gameDataUpdateRepo;
    }

    public function getLastLogSeen($idPlayer)
    {
        $player = $this->playerRepo->find($idPlayer);

        if (null === $player->getLastLog()) {
            return 0;
        } else {
            return $player->getLastLog()->getId();
        }
    }

    public function getNewGameDataUpdates($_dataLogs, $idPlayerSession)
    {
        $_gameDataUpdates = null;

        if (null != $_dataLogs) {
            foreach ($_dataLogs as $idLog => $dataLog) {
                $log = $this->logRepo->find($idLog);
                $_gameDataUpdatesTmp1 = $log->getGameDataUpdates();

                foreach ($_gameDataUpdatesTmp1 as  $gameDataUpdate) {
                    $_gameDataUpdates[$idLog][$gameDataUpdate->getId()]['idPlayer'] = $gameDataUpdate->getPlayer()->getId();
                    $_gameDataUpdates[$idLog][$gameDataUpdate->getId()]['type'] = $gameDataUpdate->getType();
                    $_gameDataUpdates[$idLog][$gameDataUpdate->getId()]['value'] = $gameDataUpdate->getValue();
                    $_gameDataUpdates[$idLog][$gameDataUpdate->getId()]['value2'] = $gameDataUpdate->getValue2();
                }
            }
        }

        $playerSession = $this->playerRepo->find($idPlayerSession);
        $game = $playerSession->getGame();

        $_logToDo = $this->getLogActionTodo($game->getId());

        if (0 !== count($_logToDo)) {
            $logToDo = $_logToDo[0];
            $idLog = $logToDo->getId();
            $_gameDataUpdatesTmp2 = $logToDo->getGameDataUpdates();

            foreach ($_gameDataUpdatesTmp2 as $gameDataUpdate) {
                $_gameDataUpdates[$idLog][$gameDataUpdate->getId()]['idPlayer'] = $gameDataUpdate->getPlayer()->getId();
                $_gameDataUpdates[$idLog][$gameDataUpdate->getId()]['type'] = $gameDataUpdate->getType();
                $_gameDataUpdates[$idLog][$gameDataUpdate->getId()]['value'] = $gameDataUpdate->getValue();
                $_gameDataUpdates[$idLog][$gameDataUpdate->getId()]['value2'] = $gameDataUpdate->getValue2();
            }
        }

        return $_gameDataUpdates;
    }

    public function getNewLogs($idPlayer)
    {
        $_dataLogs = null;

        $lastLogSeen = $this->getLastLogSeen($idPlayer);
        $playerSession = $this->playerRepo->find($idPlayer);
        $idGame = $playerSession->getGame()->getId();

        $_newLogs = $this->logRepo->getNewLogs($lastLogSeen, $idGame);

        if (0 !== count($_newLogs)) {
            foreach ($_newLogs as $log) {
                $_dataLogs[$log['idLog']]['action'] = $log['action'];
                $_dataLogs[$log['idLog']]['htmlContentLog'] = $log['message'];
            }
            ksort($_dataLogs, SORT_NUMERIC);
        }

        return $_dataLogs;
    }

    public function getAllLogs($idPlayer)
    {
        $_dataLogs = null;

        $playerSession = $this->playerRepo->find($idPlayer);

        $_logsTmp = $playerSession->getGame()->getLogs()->toArray();
        foreach ($_logsTmp as $log) {
            if (true === $log->getIsDone()) {
                $_logs[$log->getId()] = $log;
            }
        }

        if (isset($_logs)) {
            ksort($_logs);
            foreach ($_logs as $log) {
                $_dataLogs[$log->getId()]['action'] = $log->getAction();
                $_dataLogs[$log->getId()]['htmlContentLog'] = $log->getMessage();
            }
            ksort($_dataLogs, SORT_NUMERIC);
        }

        return $_dataLogs;
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
        $this->em->flush();
    }

    public function displayPlayerNameInLog(Player $playerAction)
    {
        $return = '¤'.$playerAction->getName().'µ ##monsters/to-';
        $return .= (0 === $playerAction->getInCity()) ? 'left' : 'right';
        $return .= '/regular/'.$playerAction->getMonster()->getImgName().'££';

        return $return;
    }

    public function getHTMLContentLog($action, Player $playerAction)
    {
        switch ($action) {
            case 'start_turn':
                return 'Le tour de '.$this->displayPlayerNameInLog($playerAction).' a commencé.';
                break;

            case 'is_starting_in_city':
                if (0 === $playerAction->getInCity()) {
                    return $this->displayPlayerNameInLog($playerAction)." a commencé son tour hors de Tokyo et n'a pas gagné de #symbols/star.png£";
                } else {
                    return $this->displayPlayerNameInLog($playerAction).' a commencé son tour dans Tokyo et gagné '.$this->getVictoryPointsIfInTokyo($playerAction).'#symbols/star.png£';
                }
                break;

            /*case 'init_dices':
                return  $this->displayPlayerNameInLog($playerAction).' va lancer les dés';
                break;*/

            case 'throw_dices':
                $_dicesTmp = $this->throwTokyoDiceRepo->findBy(['player' => $playerAction]);
                $dicesString = '';

                $orderType['victory'] = 3;
                $orderType['paw'] = 1;
                $orderType['heart'] = 2;
                $orderType['flash'] = 4;

                $oderFace['one'] = 3;
                $oderFace['two'] = 2;
                $oderFace['three'] = 1;
                $oderFace['paw'] = 2;
                $oderFace['heart'] = 3;
                $oderFace['flash'] = 4;

                $i = 0;
                foreach ($_dicesTmp as $dice) {
                    $_dices[$i]['typeOrder'] = $orderType[$dice->getType()];
                    $_dices[$i]['faceOrder'] = $oderFace[$dice->getFace()];
                    $_dices[$i]['obj'] = $dice;
                    ++$i;
                }

                $typeOrder = array_column($_dices, 'typeOrder');
                $faceOrder = array_column($_dices, 'faceOrder');

                array_multisort($typeOrder, SORT_ASC, $faceOrder, SORT_ASC, $_dices);

                foreach ($_dices as $dice) {
                    $dicesString .= '#dices/black/dice_'.$dice['obj']->getFace();
                    if (true === $dice['obj']->getIsKept()) {
                        $dicesString .= '_locked';
                    }
                    $dicesString .= '.png£';
                }

                $returnString = $this->displayPlayerNameInLog($playerAction).' a obtenu '.$dicesString;

                return $returnString;
                break;

            case 'resolve_order_dices':
                $_faces['one'] = 1;
                $_faces['two'] = 2;
                $_faces['three'] = 3;

                $_faces['paw'] = 1;
                $_faces['heart'] = 1;
                $_faces['flash'] = 1;

                $_resolveOrderTmp = $playerAction->getResolveOrders();
                foreach ($_resolveOrderTmp as $resolveOrder) {
                    $_resolveOrder[$resolveOrder->getResolveOrder()] = $resolveOrder->getType();
                }
                ksort($_resolveOrder);

                $_dices = $playerAction->getThrowTokyoDices();

                foreach ($_resolveOrder as $type) {
                    foreach ($_dices as $dice) {
                        if ($type === $dice->getType()) {
                            $_final[$type][$_faces[$dice->getFace()]][] = $dice->getFace();
                            ksort($_final[$type]);
                        }
                    }
                }

                $return = $this->displayPlayerNameInLog($playerAction).' a choisi cet ordre de résolution: ';
                foreach ($_final as $type => $_d) {
                    foreach ($_d as $_d2) {
                        foreach ($_d2 as $face) {
                            $return .= "#dices/black/dice_$face.png£";
                        }
                    }
                    $return .= ' ';
                }

                return $return;
                break;

            case 'resolve_dices_victory':
                return $this->displayPlayerNameInLog($playerAction).' a gagné '.$this->getVictoryPointsByDices($playerAction).'#symbols/star.png£';
                break;

            case 'resolve_dices_heart':
                return $this->displayPlayerNameInLog($playerAction).' a gagné '.$this->getHealthPointsByDices($playerAction).'#symbols/heart.png£';
                break;

            case 'resolve_dices_flash':
                return $this->displayPlayerNameInLog($playerAction).' a gagné '.$this->getManaByDices($playerAction).'#symbols/flash.png£';
                break;
            case 'resolve_dices_paw':
                $_playersAttacked = [];
                    $return = $this->displayPlayerNameInLog($playerAction).' a attaqué ';

                if (0 === $playerAction->getInCity()) {
                    $orientation = 'to-right';
                    $location = 'dans Tokyo';
                } else {
                    $orientation = 'to-left';
                    $location = 'hors de Tokyo';
                }

                $_playersEnemy = $playerAction->getGame()->getPlayers();
                if (0 === $playerAction->getInCity()) {
                    foreach ($_playersEnemy as $playerEnemy) {
                        if ($playerEnemy != $playerAction && 0 !== $playerEnemy->getInCity()) {
                            $_playersAttacked[] = $playerEnemy->getName()." ##monsters/$orientation/regular/".$playerEnemy->getMonster()->getImgName().'££';
                        }
                    }
                } else {
                    foreach ($_playersEnemy as $playerEnemy) {
                        if ($playerEnemy != $playerAction && 0 === $playerEnemy->getInCity()) {
                            $_playersAttacked[] = $playerEnemy->getName()." ##monsters/$orientation/regular/".$playerEnemy->getMonster()->getImgName().'££';
                        }
                    }
                }

                $stringPlayersAttacked = implode(', ', $_playersAttacked);
                $return .= $stringPlayersAttacked;

                $return .= " $location avec ";
                for ($i = 1; $i <= $this->getPawsByDices($playerAction); ++$i) {
                    $return .= '#dices/black/dice_paw.png£';
                }

                return $return;
                break;

            case 'ask_to_leave_tokyo':
                if (0 === $playerAction->getInCity()) {
                    $decision = ' a fui Tokyo';
                } else {
                    $decision = ' est resté dans Tokyo';
                }

                return $this->displayPlayerNameInLog($playerAction).$decision;
                break;

            case 'are_dead':
                $logMsg = '';
                $_deadPlayersMsg = [];

                $_logToDo = $this->getLogActionTodo($playerAction->getGame()->getId());
                if (0 !== count($_logToDo)) {
                    $logToDo = $_logToDo[0];

                    $_gameDataUpdates = $logToDo->getGameDataUpdates();
                    foreach ($_gameDataUpdates as $gdu) {
                        if ('dead' === $gdu->getType()) {
                            $_deadPlayersMsg[] = $this->displayPlayerNameInLog($gdu->getPlayer()).' est #/symbols/dead.png£ !';
                        }
                    }
                }
                $logMsg .= implode(', ', $_deadPlayersMsg);

                return $logMsg;
                break;

            case 'enter_tokyo_city':
                return $this->displayPlayerNameInLog($playerAction).' est entré dans Tokyo City et a gagné 1#symbols/star.png£';
                break;
            case 'enter_tokyo_bay':
                return $this->displayPlayerNameInLog($playerAction).' est entré dans Tokyo Bay et a gagné 1#symbols/star.png£';
                break;
            case 'out_of_tokyo_bay':
                return $this->displayPlayerNameInLog($playerAction).' est sorti de Tokyo Bay car il reste moins de 5 joueurs en jeu';
                break;
            case 'enter_tokyo_city_from_tokyo_bay':
                return $this->displayPlayerNameInLog($playerAction).' est passé de Tokyo Bay à Tokyo City car il reste moins de 5 joueurs en jeu';
                break;

            default:
                return "$action: LOG A CREER";
                break;
        }
    }

    // Teste si le joueur de la session est celui ayant une action de jeu à effectuer
    public function hasActionToDo($idPlayerSession)
    {
        $playerSession = $this->playerRepo->find($idPlayerSession);
        $game = $playerSession->getGame();

        $_logToDo = $this->getLogActionTodo($game->getId());
        if (0 !== count($_logToDo)) {
            $logToDo = $_logToDo[0];
            $idPlayerToPlay = $logToDo->getPlayer()->getId();

            return $idPlayerSession == $idPlayerToPlay;
        } else {
            return false;
        }
    }

    public function getLogActionTodo($idGame)
    {
        return $this->logRepo->findLogToDoByGame($idGame);
    }

    public function findLog($idLog)
    {
        return $this->logRepo->find($idLog);
    }

    public function validAction($idPlayerSession, $data)
    {
        $playerAction = $this->playerRepo->find($idPlayerSession);
        $game = $playerAction->getGame();

        $_logDone = $this->getLogActionTodo($game->getId());
        $logDone = $_logDone[0];

        $logDone->setIsDone(true);

        $nextLog = new Log();
        $nextLog->setIsDone(false);
        $nextLog->setGame($game);

        $nameAction = $logDone->getAction();

        switch ($nameAction) {
            case 'start_turn':
                $nextAction = 'is_starting_in_city';

                $nextLog->setAction($nextAction);
                $nextLog->setPlayer($playerAction);

                $this->prepareNextAction($nextLog);

                $logDone->setMessage($this->getHTMLContentLog($nameAction, $playerAction));
                break;

            case 'is_starting_in_city':
                $nextAction = 'throw_dices';

                $nextLog->setAction($nextAction);
                $nextLog->setPlayer($playerAction);

                $this->prepareNextAction($nextLog);

                $logDone->setMessage($this->getHTMLContentLog($nameAction, $playerAction));
                break;

            case 'throw_dices':
                $logDone->setMessage($this->getHTMLContentLog($nameAction, $playerAction));

                $areAllKept = false;
                if (isset($data) && is_array($data)) {
                    $areAllKept = true;
                    foreach ($data as $k => $v) {
                        $dice[$k] = $this->throwTokyoDiceRepo->findBy(['player' => $playerAction, 'position' => $k]);
                        $isKept = ('true' === $v) ? true : false;

                        $dice[$k][0]->setIsKept($isKept);
                        if (!$isKept) {
                            $areAllKept = false;
                        }
                    }
                }
                if ($playerAction->getThrowsLeft() > 0 && !$areAllKept) {
                    $nextAction = 'throw_dices';
                } else {
                    $_dicesByType['victory'] = 0;
                    $_dicesByType['heart'] = 0;
                    $_dicesByType['flash'] = 0;
                    $_dicesByType['paw'] = 0;

                    $_dices = $playerAction->getThrowTokyoDices();
                    foreach ($_dices as $dice) {
                        ++$_dicesByType[$dice->getType()];
                    }

                    foreach ($_dicesByType as $type => $nb) {
                        if (0 === $nb) {
                            unset($_dicesByType[$type]);
                        } else {
                            $uniqueRsolve = $type;
                        }
                    }

                    if (count($_dicesByType) > 1) {
                        $nextAction = 'resolve_order_dices';
                    } else {
                        $_resolveOrders = $playerAction->getResolveOrders();
                        foreach ($_resolveOrders as $resolveOrder) {
                            $playerAction->removeResolveOrder($resolveOrder);
                        }

                        foreach ($_dicesByType as $type => $d) {
                            $_resolveOrder[1] = new ResolveOrder();
                            $_resolveOrder[1]->setPlayer($playerAction);
                            $_resolveOrder[1]->setResolveOrder(1);
                            $_resolveOrder[1]->setType($type);

                            $this->em->persist($_resolveOrder[1]);
                        }

                        $this->em->persist($_resolveOrder[1]);
                        $nextAction = 'resolve_dices_'.$uniqueRsolve;
                    }
                }

                $nextLog->setAction($nextAction);
                $nextLog->setPlayer($playerAction);

                $this->prepareNextAction($nextLog);
                break;

            case 'resolve_order_dices':
                // On enregistre l'ordre de résolution
                $_resolveOrders = $playerAction->getResolveOrders();
                foreach ($_resolveOrders as $resolveOrder) {
                    $playerAction->removeResolveOrder($resolveOrder);
                }

                foreach ($data as $order => $type) {
                    $_resolveOrder[$order] = new ResolveOrder();
                    $_resolveOrder[$order]->setPlayer($playerAction);
                    $_resolveOrder[$order]->setResolveOrder($order);
                    $_resolveOrder[$order]->setType($type);

                    $this->em->persist($_resolveOrder[$order]);
                }
                $this->em->flush();
                $this->em->refresh($playerAction);

                $nextLog->setAction('resolve_dices_'.$data[1]);
                $nextLog->setPlayer($playerAction);

                $this->prepareNextAction($nextLog);

                $logDone->setMessage($this->getHTMLContentLog($nameAction, $playerAction));
                break;

            case 'resolve_dices_victory':
                $nextAction = $this->getNextResolve($playerAction, 'victory');
                $nextPlayer = $playerAction;
                if (!$nextAction) {
                    $nextAction = $this->getActionNewPosition($playerAction);
                }
                if (!$nextAction) {
                    $nextAction = $this->getActionAfterNewPosition($playerAction);
                    $nextPlayer = $this->findNextPlayer($game, $playerAction);
                }

                $nextLog->setAction($nextAction);
                $nextLog->setPlayer($nextPlayer);

                $this->prepareNextAction($nextLog);

                $logDone->setMessage($this->getHTMLContentLog($nameAction, $playerAction));

                break;

            case 'resolve_dices_heart':
                $nextAction = $this->getNextResolve($playerAction, 'heart');
                if (!$nextAction) {
                    $nextAction = $this->getActionNewPosition($playerAction);
                }
                $nextPlayer = $playerAction;
                if (!$nextAction) {
                    $nextAction = $this->getActionAfterNewPosition($playerAction);
                    $nextPlayer = $this->findNextPlayer($game, $playerAction);
                }

                $nextLog->setAction($nextAction);
                $nextLog->setPlayer($nextPlayer);

                $this->prepareNextAction($nextLog);

                $logDone->setMessage($this->getHTMLContentLog($nameAction, $playerAction));
                break;

            case 'resolve_dices_flash':
                $nextAction = $this->getNextResolve($playerAction, 'flash');
                $nextPlayer = $playerAction;
                if (!$nextAction) {
                    $nextAction = $this->getActionNewPosition($playerAction);
                }
                if (!$nextAction) {
                    $nextAction = $this->getActionAfterNewPosition($playerAction);
                    $nextPlayer = $this->findNextPlayer($game, $playerAction);
                }

                $nextLog->setAction($nextAction);
                $nextLog->setPlayer($nextPlayer);

                $this->prepareNextAction($nextLog);

                $logDone->setMessage($this->getHTMLContentLog($nameAction, $playerAction));
                break;

            case 'resolve_dices_paw':
                $nextAction = null;
                $nextPlayer = null;

                $secondAction = null;
                $secondPlayer = null;

                // EST CE QUE QUELQU'UN EST MORT ?
                $nbDeadPlayers = 0;
                $_players = $playerAction->getGame()->getPlayers();
                foreach ($_players as $playerEnemy) {
                    if (0 === $playerEnemy->getHp() && true === $playerEnemy->getIsAlive()) {
                        $playerEnemy->setInCity(0);
                        ++$nbDeadPlayers;
                    }
                }

                // EST CE QU'ON A TAPE QUELQU'UN DANS TOKYO et QUI N'EST PAS MORT ?
                $nbAttackedInTokyo = 0;
                if (0 === $playerAction->getInCity() && 0 !== $this->getPawsByDices($playerAction)) {
                    $_players = $playerAction->getGame()->getPlayers();

                    foreach ($_players as $playerEnemy) {
                        if (0 !== $playerEnemy->getInCity() && $playerEnemy->getHp() > 0) {
                            $_playersInTokyo[$nbAttackedInTokyo] = $playerEnemy;
                            ++$nbAttackedInTokyo;
                        }
                    }
                }

                if (0 !== $nbDeadPlayers) {
                    $nextAction = 'are_dead';
                    $nextPlayer = $playerAction;

                    if (0 !== $nbAttackedInTokyo) {
                        $secondAction = 'ask_to_leave_tokyo';
                        $secondPlayer = $_playersInTokyo[0];
                    } else {
                        $secondAction = $this->getNextResolve($playerAction, 'paw');
                        $secondPlayer = $playerAction;
                        if (!$secondAction) {
                            $secondAction = $this->getActionNewPosition($playerAction);
                        }
                        if (!$secondAction) {
                            $secondAction = $this->getActionAfterNewPosition($playerAction);
                            $secondPlayer = $this->findNextPlayer($game, $playerAction);
                        }
                    }
                } else {
                    if (0 !== $nbAttackedInTokyo) {
                        $nextAction = 'ask_to_leave_tokyo';
                        $nextPlayer = $_playersInTokyo[0];
                    } else {
                        $nextPlayer = $playerAction;
                        $nextAction = $this->getNextResolve($playerAction, 'paw');
                        if (!$nextAction) {
                            $nextAction = $this->getActionNewPosition($playerAction);
                        }
                        if (!$nextAction) {
                            $nextAction = $this->getActionAfterNewPosition($playerAction);
                            $nextPlayer = $this->findNextPlayer($game, $playerAction);
                        }
                    }
                }

                $logDone->setMessage($this->getHTMLContentLog($nameAction, $playerAction));

                $nextLog->setAction($nextAction);
                $nextLog->setPlayer($nextPlayer);
                $nextLog->setNextAction($secondAction);
                $nextLog->setNextPlayer($secondPlayer);

                $this->prepareNextAction($nextLog);
                break;

            case 'are_dead':
                $_players = $game->getPlayers();
                $nbAlive = 0;
                foreach ($_players as $player) {
                    if (true === $player->getIsAlive()) {
                        ++$nbAlive;
                    }
                }
                if (1 === $nbAlive) {
                    $_players = $game->getPlayers();
                    foreach ($_players as $player) {
                        if (true === $player->getIsPlaying()) {
                            $nextPlayer = $player;
                            break;
                        }
                    }
                    $nextLog->setAction('has_won_by_killing');
                    $nextLog->setPlayer($nextPlayer);
                } else {
                    $nextLog->setAction($logDone->getNextAction());
                    $nextLog->setPlayer($logDone->getNextPlayer());
                }
                $this->prepareNextAction($nextLog);
                $logDone->setMessage($this->getHTMLContentLog($nameAction, $playerAction));
                break;

            case 'ask_to_leave_tokyo':
                $playerAction->setHasDecidedAboutTokyo(true);

                if (2 == $data) {
                    $gameDataUpdate = new GameDataUpdate();
                    $gameDataUpdate->setLog($nextLog);
                    $gameDataUpdate->setPlayer($playerAction);
                    $gameDataUpdate->setType('out_of_tokyo');
                    $gameDataUpdate->setValue($playerAction->getInCity());
                    $this->em->persist($gameDataUpdate);

                    $playerAction->setInCity(0);
                }

                $nbAttackedInTokyo = 0;
                $_players = $game->getPlayers();
                foreach ($_players as $playerEnemy) {
                    if (0 !== $playerEnemy->getInCity() && false === $playerEnemy->getHasDecidedAboutTokyo()) {
                        $_playersInTokyo[$nbAttackedInTokyo] = $playerEnemy;
                        ++$nbAttackedInTokyo;
                    }
                }

                if (0 !== $nbAttackedInTokyo) {
                    $nextAction = 'ask_to_leave_tokyo';
                    $nextPlayer = $_playersInTokyo[0];
                } else {
                    $nextPlayer = $this->getPlayerPlaying($game);
                    $nextAction = $this->getNextResolve($nextPlayer, 'paw');
                    if (!$nextAction) {
                        $nextAction = $this->getActionNewPosition($nextPlayer);
                    }
                    if (!$nextAction) {
                        $nextAction = $this->getActionAfterNewPosition($playerAction);
                        $nextPlayer = $this->findNextPlayer($game, $nextPlayer);
                    }
                }

                $nextLog->setAction($nextAction);
                $nextLog->setPlayer($nextPlayer);
                if (0 === $nbAttackedInTokyo) {
                    $this->resumeTurn($nextLog);
                }
                $this->prepareNextAction($nextLog);

                $logDone->setMessage($this->getHTMLContentLog($nameAction, $playerAction));
                break;

            case 'enter_tokyo_city':
            case 'enter_tokyo_bay':
            case 'enter_tokyo_city_from_tokyo_bay':
            case 'out_of_tokyo_bay':
                if (20 === $playerAction->getVp()) {
                    $nextAction = 'has_won_by_victory_points';
                    $nextPlayer = $playerAction;
                } else {
                    $nextAction = $this->getActionAfterNewPosition($playerAction);
                    $nextPlayer = $this->findNextPlayer($game, $playerAction);
                }

                $logDone->setMessage($this->getHTMLContentLog($nameAction, $playerAction));

                $nextLog->setAction($nextAction);
                $nextLog->setPlayer($nextPlayer);

                $this->prepareNextAction($nextLog);
                break;

            case 'has_won_by_victory_points':
                $nextAction = $this->getActionAfterNewPosition($playerAction);
                $logDone->setMessage($this->getHTMLContentLog($nameAction, $playerAction));

                $nextLog->setAction($nextAction);
                $nextLog->setPlayer($playerAction);

                $this->prepareNextAction($nextLog);
                break;
        }
        $this->em->persist($nextLog);
        $this->em->flush();
        $this->em->refresh($nextLog);

        return true;
    }

    public function getActionAfterNewPosition(Player $playerACtion)
    {
        return 'start_turn';
    }

    public function findPlayerInTokyoBay(Game $game)
    {
        $this->em->refresh($game);
        $_players = $game->getPlayers();

        foreach ($_players as $player) {
            $this->em->refresh($player);
            if (2 === $player->getInCity()) {
                return $player;
                break;
            }
        }

        return false;
    }

    public function prepareNextAction(Log $nextLog)
    {
        $nextAction = $nextLog->getAction();
        $playerAction = $nextLog->getPlayer();
        $game = $nextLog->getGame();

        switch ($nextAction) {
            case 'start_turn':
                $_players = $nextLog->getGame()->getPlayers();
                foreach ($_players as $player) {
                    if (true === $player->getIsPlaying()) {
                        $formerPlayer = $player;
                        $player->setIsPlaying(false);
                    }
                    $player->setHasDecidedAboutTokyo(false);
                }

                $playerAction->setIsPlaying(true);

                $playerAction->setThrowsLeft(3);

                $throws = $playerAction->getThrowTokyoDices();
                foreach ($throws as $throw) {
                    $playerAction->removeThrowTokyoDice($throw);
                }

                $resolveOrders = $playerAction->getResolveOrders();
                foreach ($resolveOrders as $resolveOrder) {
                    $playerAction->removeResolveOrder($resolveOrder);
                }

                $gameDataUpdateEnd = new GameDataUpdate();
                $gameDataUpdateEnd->setLog($nextLog);
                $gameDataUpdateEnd->setPlayer($formerPlayer);
                $gameDataUpdateEnd->setType('end_turn');
                $gameDataUpdateEnd->setValue('');
                $this->em->persist($gameDataUpdateEnd);

                $gameDataUpdateStart = new GameDataUpdate();
                $gameDataUpdateStart->setLog($nextLog);
                $gameDataUpdateStart->setPlayer($playerAction);
                $gameDataUpdateStart->setType('start_turn');
                $gameDataUpdateStart->setValue($playerAction->getName());
                $orientation = (0 === $playerAction->getInCity()) ? 'left' : 'right';
                $gameDataUpdateStart->setValue2("/img/monsters/to-$orientation/regular/".$playerAction->getMonster()->getImgName());
                $this->em->persist($gameDataUpdateStart);
                break;

            case 'is_starting_in_city':
                if (0 !== $playerAction->getInCity()) {
                    $this->addVictoryPointsIfInCity($playerAction);
                }

                $gameDataUpdate = new GameDataUpdate();
                $gameDataUpdate->setLog($nextLog);
                $gameDataUpdate->setPlayer($playerAction);
                $gameDataUpdate->setType('vp');
                $gameDataUpdate->setValue($playerAction->getVp());
                $this->em->persist($gameDataUpdate);
                break;

            case 'throw_dices':
                $this->throwDices($playerAction);
                break;

            case 'resolve_dices_victory':
                $this->addVictoryPointsByDices($playerAction);

                $gameDataUpdate = new GameDataUpdate();
                $gameDataUpdate->setLog($nextLog);
                $gameDataUpdate->setPlayer($playerAction);
                $gameDataUpdate->setType('vp');
                $gameDataUpdate->setValue($playerAction->getVp());
                $this->em->persist($gameDataUpdate);
                break;

            case 'resolve_dices_heart':
                $points = $this->addHealthPointsByDices($playerAction);

                $gameDataUpdate = new GameDataUpdate();
                $gameDataUpdate->setLog($nextLog);
                $gameDataUpdate->setPlayer($playerAction);
                $gameDataUpdate->setType('hp');
                $gameDataUpdate->setValue($playerAction->getHp());
                $gameDataUpdate->setValue2($playerAction->getHpMax());
                $this->em->persist($gameDataUpdate);
                break;

            case 'resolve_dices_flash':
                $this->addManaByDices($playerAction);

                $gameDataUpdate = new GameDataUpdate();
                $gameDataUpdate->setLog($nextLog);
                $gameDataUpdate->setPlayer($playerAction);
                $gameDataUpdate->setType('mana');
                $gameDataUpdate->setValue($playerAction->getNbMana());
                $this->em->persist($gameDataUpdate);
                break;

            case 'resolve_dices_paw':
                $_playersAttacked = $this->resolvePaws($playerAction);

                $nbGDU = 0;
                foreach ($_playersAttacked as $k => $playerAttacked) {
                    $gameDataUpdate[$nbGDU] = new GameDataUpdate();
                    $gameDataUpdate[$nbGDU]->setLog($nextLog);
                    $gameDataUpdate[$nbGDU]->setPlayer($playerAttacked);
                    $gameDataUpdate[$nbGDU]->setType('hp');
                    $gameDataUpdate[$nbGDU]->setValue($playerAttacked->getHp());
                    $gameDataUpdate[$nbGDU]->setValue2($playerAttacked->getHpMax());
                    $this->em->persist($gameDataUpdate[$nbGDU]);
                    ++$nbGDU;
                }
                break;

            case 'ask_to_leave_tokyo':
                $gameDataUpdate = new GameDataUpdate();
                $gameDataUpdate->setLog($nextLog);
                $gameDataUpdate->setPlayer($playerAction);
                $gameDataUpdate->setType('interrupting_action');
                $gameDataUpdate->setValue($playerAction->getName());
                $orientation = (0 === $playerAction->getInCity()) ? 'left' : 'right';
                $gameDataUpdate->setValue2("/img/monsters/to-$orientation/regular/".$playerAction->getMonster()->getImgName());
                $this->em->persist($gameDataUpdate);
                break;

            case 'are_dead':
                $nbGDU = 0;
                $_players = $playerAction->getGame()->getPlayers();
                foreach ($_players as $playerEnemy) {
                    if (0 === $playerEnemy->getHp() && true === $playerEnemy->getIsAlive()) {
                        if (0 !== $playerEnemy->getInCity()) {
                            $gameDataUpdate[$nbGDU] = new GameDataUpdate();
                            $gameDataUpdate[$nbGDU]->setLog($nextLog);
                            $gameDataUpdate[$nbGDU]->setPlayer($playerEnemy);
                            $gameDataUpdate[$nbGDU]->setType('out_of_tokyo');
                            $gameDataUpdate[$nbGDU]->setValue($playerEnemy->getInCity());
                            $this->em->persist($gameDataUpdate[$nbGDU]);
                            ++$nbGDU;
                        }

                        $gameDataUpdate[$nbGDU] = new GameDataUpdate();
                        $gameDataUpdate[$nbGDU]->setLog($nextLog);
                        $gameDataUpdate[$nbGDU]->setPlayer($playerEnemy);
                        $gameDataUpdate[$nbGDU]->setType('dead');
                        $gameDataUpdate[$nbGDU]->setValue('dead');
                        $this->em->persist($gameDataUpdate[$nbGDU]);

                        $this->killPlayer($playerEnemy);
                    }
                }
                break;

            case 'enter_tokyo_city':
                $playerAction->setInCity(1);
                $this->addVictoryPointsEnteringCity($playerAction);

                $gameDataUpdateEnter = new GameDataUpdate();
                $gameDataUpdateEnter->setLog($nextLog);
                $gameDataUpdateEnter->setPlayer($playerAction);
                $gameDataUpdateEnter->setType('in_tokyo');
                $gameDataUpdateEnter->setValue(1);
                $gameDataUpdateEnter->setValue2($playerAction->getMonster()->getImgName());
                $this->em->persist($gameDataUpdateEnter);

                $gameDataUpdateVp = new GameDataUpdate();
                $gameDataUpdateVp->setLog($nextLog);
                $gameDataUpdateVp->setPlayer($playerAction);
                $gameDataUpdateVp->setType('vp');
                $gameDataUpdateVp->setValue($playerAction->getVp());
                $this->em->persist($gameDataUpdateVp);
                break;

            case 'enter_tokyo_bay':
                $playerAction->setInCity(2);
                $this->addVictoryPointsEnteringCity($playerAction);

                $gameDataUpdateEnter = new GameDataUpdate();
                $gameDataUpdateEnter->setLog($nextLog);
                $gameDataUpdateEnter->setPlayer($playerAction);
                $gameDataUpdateEnter->setType('in_tokyo');
                $gameDataUpdateEnter->setValue(2);
                $gameDataUpdateEnter->setValue2($playerAction->getMonster()->getImgName());
                $this->em->persist($gameDataUpdateEnter);

                $gameDataUpdateVp = new GameDataUpdate();
                $gameDataUpdateVp->setLog($nextLog);
                $gameDataUpdateVp->setPlayer($playerAction);
                $gameDataUpdateVp->setType('vp');
                $gameDataUpdateVp->setValue($playerAction->getVp());
                $this->em->persist($gameDataUpdateVp);
                break;

            case 'out_of_tokyo_bay':
                $playerAction->setInCity(0);
                $gameDataUpdate = new GameDataUpdate();
                $gameDataUpdate->setLog($nextLog);
                $gameDataUpdate->setPlayer($playerAction);
                $gameDataUpdate->setType('out_of_tokyo');
                $gameDataUpdate->setValue(2);
                $this->em->persist($gameDataUpdate);
                break;

            case 'enter_tokyo_city_from_tokyo_bay':
                $playerAction->setInCity(1);

                $gameDataUpdateLeave = new GameDataUpdate();
                $gameDataUpdateLeave->setLog($nextLog);
                $gameDataUpdateLeave->setPlayer($playerAction);
                $gameDataUpdateLeave->setType('out_of_tokyo');
                $gameDataUpdateLeave->setValue(2);
                $this->em->persist($gameDataUpdateLeave);

                $gameDataUpdateEnter = new GameDataUpdate();
                $gameDataUpdateEnter->setLog($nextLog);
                $gameDataUpdateEnter->setPlayer($playerAction);
                $gameDataUpdateEnter->setType('in_tokyo');
                $gameDataUpdateEnter->setValue(1);
                $gameDataUpdateEnter->setValue2($playerAction->getMonster()->getImgName());
                $this->em->persist($gameDataUpdateEnter);
                break;

            case 'has_won_by_killing':
                $playerAction->setInCity(0);
                $gameDataUpdate = new GameDataUpdate();
                $gameDataUpdate->setLog($nextLog);
                $gameDataUpdate->setPlayer($playerAction);
                $gameDataUpdate->setType('has_won_by_killing');
                $gameDataUpdate->setValue($playerAction->getName());
                $gameDataUpdate->setValue2($playerAction->getMonster()->getImgName());
                $this->em->persist($gameDataUpdate);

                $playerAction->setIsWinner(true);
                $game->setWinner($playerAction);
                $game->setVictoryType('kills');
                $game->setState(3);
                $game->setFinishedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
                break;

            case 'has_won_by_victory_points':
                $playerAction->setInCity(0);
                $gameDataUpdate = new GameDataUpdate();
                $gameDataUpdate->setLog($nextLog);
                $gameDataUpdate->setPlayer($playerAction);
                $gameDataUpdate->setType('has_won_by_victory_points');
                $gameDataUpdate->setValue($playerAction->getName());
                $gameDataUpdate->setValue2($playerAction->getMonster()->getImgName());
                $this->em->persist($gameDataUpdate);

                $playerAction->setIsWinner(true);
                $game->setWinner($playerAction);
                $game->setVictoryType('points');
                $game->setState(3);
                $game->setFinishedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
                break;
        }
    }

    public function resumeTurn(Log $nextLog)
    {
        $nextPlayer = $nextLog->getPlayer();

        $gameDataUpdate = new GameDataUpdate();
        $gameDataUpdate->setLog($nextLog);
        $gameDataUpdate->setPlayer($nextPlayer);
        $gameDataUpdate->setType('resuming_turn');
        $gameDataUpdate->setValue($nextPlayer->getName());
        $orientation = (0 === $nextPlayer->getInCity()) ? 'left' : 'right';
        $gameDataUpdate->setValue2("/img/monsters/to-$orientation/regular/".$nextPlayer->getMonster()->getImgName());
        $this->em->persist($gameDataUpdate);
    }

    public function getPlayerPlaying(Game $game)
    {
        $nextPlayer = null;
        $_players = $game->getPlayers();
        foreach ($_players as $player) {
            if (true === $player->getIsPlaying()) {
                $nextPlayer = $player;
                break;
            }
        }

        return $nextPlayer;
    }

    public function getActionNewPosition(Player $playerAction)
    {
        $this->em->refresh($playerAction);

        $tokyoCityOccupied = false;
        $nbAlive = 0;
        $tokyoBayOccupied = false;

        $_players = $playerAction->getGame()->getPlayers();
        foreach ($_players as $player) {
            if (1 === $player->getInCity()) {
                $tokyoCityOccupied = true;
            }
            if (2 === $player->getInCity()) {
                $tokyoBayOccupied = true;
            }
            if (0 < $player->getHp()) {
                ++$nbAlive;
            }
        }

        switch ($playerAction->getInCity()) {
            case 0:
                if (!$tokyoCityOccupied) {
                    return 'enter_tokyo_city';
                } else {
                    if ($nbAlive > 2) {
                        if (!$tokyoBayOccupied) {
                            return 'enter_tokyo_bay';
                        }
                    }
                }
                break;

            case 1:
                return false;
                break;

            case 2:
                if ($nbAlive <= 2) {
                    if (!$tokyoCityOccupied) {
                        return 'enter_tokyo_city_from_tokyo_bay';
                    } else {
                        return 'out_of_tokyo_bay';
                    }
                }
                break;
        }

        return false;
    }

    public function getNextResolve(Player $playerAction, $actualResolve)
    {
        $_resolveOrders = $playerAction->getResolveOrders()->toArray();
        foreach ($_resolveOrders as $resolveOrder) {
            if ($actualResolve === $resolveOrder->getType()) {
                $actualOrder = $resolveOrder->getResolveOrder();
            }
            $_resolveOrdersOrdered[$resolveOrder->getResolveOrder()] = $resolveOrder->getType();
        }
        ksort($_resolveOrdersOrdered);

        if (array_key_exists($actualOrder + 1, $_resolveOrdersOrdered)) {
            return 'resolve_dices_'.$_resolveOrdersOrdered[$actualOrder + 1];
        } else {
            return false;
        }
    }

    public function killPlayer(Player $deadPlayer)
    {
        $deadPlayer->setInCity(0);
        $deadPlayer->setIsAlive(false);
    }

    public function attackWithPaws(Player $playerEnemy, $paws)
    {
        $playerEnemy->setHp(max(0, $playerEnemy->getHp() - $paws));

        return $playerEnemy;
    }

    public function resolvePaws(Player $playerAction)
    {
        $_playersAttacked = [];
        $paws = $this->getPawsByDices($playerAction);

        if (0 !== $paws) {
            $_playersEnemy = $playerAction->getGame()->getPlayers();

            if (0 === $playerAction->getInCity()) {
                foreach ($_playersEnemy as $playerEnemy) {
                    if ($playerEnemy != $playerAction && 0 !== $playerEnemy->getInCity()) {
                        $_playersAttacked[] = $this->attackWithPaws($playerEnemy, $paws);
                    }
                }
            } else {
                foreach ($_playersEnemy as $playerEnemy) {
                    if ($playerEnemy != $playerAction && 0 === $playerEnemy->getInCity()) {
                        $_playersAttacked[] = $this->attackWithPaws($playerEnemy, $paws);
                    }
                }
            }
        }

        return $_playersAttacked;
    }

    public function findNextPlayer(Game $game, Player $playerAction)
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

    public function getVictoryPointsIfInTokyo(Player $player)
    {
        return 2;
    }

    public function addVictoryPointsIfInCity(Player $player)
    {
        $pointsWon = $this->getVictoryPointsIfInTokyo($player);
        $player->setVp(min(20, $player->getVp() + $pointsWon));

        return $pointsWon;
    }

    public function getVictoryPointsEnteringokyo(Player $player)
    {
        return 1;
    }

    public function addVictoryPointsEnteringCity(Player $player)
    {
        $pointsWon = $this->getVictoryPointsEnteringokyo($player);
        $player->setVp(min(20, $player->getVp() + $pointsWon));

        return $pointsWon;
    }

    public function throwDices(Player $player)
    {
        $_dices_result = [];

        $_dicesObj = $this->throwTokyoDiceRepo->findBy(['player' => $player]);
        $_dicesArr = [];
        foreach ($_dicesObj as $dice) {
            $_dicesArr[$dice->getPosition()] = true;
            if (false === $dice->getIsKept()) {
                $this->em->remove($dice);
                unset($_dicesArr[$dice->getPosition()]);
            }
        }
        //$this->em->flush();

        $_faces[1] = 'one';
        $_faces[2] = 'two';
        $_faces[3] = 'three';
        $_faces[4] = 'paw';
        $_faces[5] = 'heart';
        $_faces[6] = 'flash';

        $nbDices = $player->getNbDices();

        for ($i = 1; $i <= $nbDices; ++$i) {
            if (!isset($_dicesArr[$i])) {
                $result = random_int(1, 6);
                $_dices_result[$i] = $result;

                $type = ($result <= 3) ? 'victory' : $_faces[$result];

                $dice = new ThrowTokyoDice();
                $dice->setPlayer($player);
                $dice->setFace($_faces[$result]);
                $dice->setIsKept(false);
                $dice->setPosition($i);
                $dice->setType($type);
                $this->em->persist($dice);
            }
        }

        $player->setThrowsLeft(max($player->getThrowsLeft() - 1, 0));
        //$this->em->flush();

        return $_dices_result;
    }

    public function getNbDicesPaws(Player $player)
    {
        $paws = 0;
        $_dices = $player->getThrowTokyoDices();
        foreach ($_dices as $dice) {
            if ('paw' === $dice->getType()) {
                ++$paws;
            }
        }

        return $paws;
    }

    public function getPawsByDices(Player $player)
    {
        return $this->getNbDicesPaws($player);
    }

    public function getNbDicesMana(Player $player)
    {
        $mana = 0;
        $_dices = $player->getThrowTokyoDices();
        foreach ($_dices as $dice) {
            if ('flash' === $dice->getType()) {
                ++$mana;
            }
        }

        return $mana;
    }

    public function getManaByDices(Player $player)
    {
        return $this->getNbDicesMana($player);
    }

    public function addManaByDices(Player $player)
    {
        $mana = $this->getManaByDices($player);
        $player->setNbMana($player->getNbMana() + $mana);
        //$this->em->flush();

        return $mana;
    }

    public function getNbDicesHeart(Player $player)
    {
        $healthPoints = 0;
        $_dices = $player->getThrowTokyoDices();

        foreach ($_dices as $dice) {
            if ('heart' === $dice->getType()) {
                ++$healthPoints;
            }
        }

        return $healthPoints;
    }

    public function getHealthPointsByDices(Player $player)
    {
        $healthPoints = 0;
        if (0 === $player->getInCity()) {
            $healthPoints = $this->getNbDicesHeart($player);
        }

        return $healthPoints;
    }

    public function addHealthPointsByDices(Player $player)
    {
        $formerHp = $player->getHp();

        $healthPointsWon = $this->getHealthPointsByDices($player);
        $player->setHp(min($player->getHpMax(), $player->getHp() + $healthPointsWon));
        //$this->em->flush();

        return [$formerHp, $player->getHp()];
    }

    public function getNbDicesVictory(Player $player)
    {
        $_nbDices['one'] = 0;
        $_nbDices['two'] = 0;
        $_nbDices['three'] = 0;

        $_dices = $player->getThrowTokyoDices();
        foreach ($_dices as $dice) {
            if ('victory' === $dice->getType()) {
                ++$_nbDices[$dice->getFace()];
            }
        }

        return $_nbDices;
    }

    public function getVictoryPointsByDices(Player $player)
    {
        $_victoryPoints['one'] = 1;
        $_victoryPoints['two'] = 2;
        $_victoryPoints['three'] = 3;

        $_nbDices = $this->getNbDicesVictory($player);

        $victoryPoints = 0;
        foreach ($_nbDices as $value => $nb) {
            if ($nb >= 3) {
                $victoryPoints += $_victoryPoints[$value] + ($nb - 3);
            }
        }

        return $victoryPoints;
    }

    public function addVictoryPointsByDices(Player $player)
    {
        $victoryPoints = $this->getVictoryPointsByDices($player);
        $player->setVp(min(20, $player->getVp() + $victoryPoints));
        //$this->em->flush();

        return $victoryPoints;
    }
}
