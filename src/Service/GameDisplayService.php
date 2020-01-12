<?php

namespace App\Service;

use App\Entity\Player;
use App\Repository\GameRepository;
use App\Repository\LogRepository;
use App\Repository\PlayerRepository;
use App\Repository\ThrowTokyoDiceRepository;
use Doctrine\ORM\EntityManagerInterface;

class GameDisplayService
{
    private $em;

    private $playerRepo;
    private $logRepo;
    private $gameRepo;
    private $throwTokyoDiceRepo;
    private $gamePlayService;

    public function __construct(PlayerRepository $playerRepo, LogRepository $logRepo, GameRepository $gameRepo, ThrowTokyoDiceRepository $throwTokyoDiceRepo, GamePlayService $gamePlayService, EntityManagerInterface $em)
    {
        $this->em = $em;

        $this->playerRepo = $playerRepo;
        $this->logRepo = $logRepo;
        $this->gameRepo = $gameRepo;
        $this->throwTokyoDiceRepo = $throwTokyoDiceRepo;
        $this->gamePlayService = $gamePlayService;
    }

    public function displayActionToDo($idPlayerSession)
    {
        $playerSession = $this->playerRepo->find($idPlayerSession);
        $game = $playerSession->getGame();

        $_logToDo = $this->gamePlayService->getLogActionTodo($game->getId());
        if (0 !== count($_logToDo)) {
            $logToDo = $_logToDo[0];
            $_dataAction['nameAction'] = $logToDo->getAction();
            $playerAction = $logToDo->getPlayer();

            $playerSessionIsPlaying = ($playerAction === $playerSession);

            switch ($_dataAction['nameAction']) {
                case 'start_turn':
                    $_dataDivAction = $this->getActionStartTurn($playerAction, $playerSessionIsPlaying);
                    break;

                case 'is_starting_in_city':
                    $_dataDivAction = $this->getActionIsStartingInCity($playerAction, $playerSessionIsPlaying);
                    break;

                case 'throw_dices':
                    $_dataDivAction = $this->getActionThrowDices($playerAction, $playerSessionIsPlaying);
                    break;

                case 'resolve_order_dices':
                    $_dataDivAction = $this->getActionResolveOrderDices($playerAction, $playerSessionIsPlaying);
                    break;

                case 'resolve_dices_victory':
                    $_dataDivAction = $this->getActionResolveDicesVictory($playerAction, $playerSessionIsPlaying);
                    break;

                case 'resolve_dices_heart':
                $_dataDivAction = $this->getActionResolveDicesHeart($playerAction, $playerSessionIsPlaying);
                break;

                case 'resolve_dices_flash':
                    $_dataDivAction = $this->getActionResolveDicesMana($playerAction, $playerSessionIsPlaying);
                    break;

                case 'resolve_dices_paw':
                    $_dataDivAction = $this->getActionResolveDicesPaws($playerAction, $playerSessionIsPlaying);
                    break;

                case 'ask_to_leave_tokyo':
                    $_dataDivAction = $this->getActionAskToLeaveTokyo($playerAction, $playerSessionIsPlaying);
                    break;

                case 'are_dead':
                    $_dataDivAction = $this->getActionAreDead($playerAction, $playerSessionIsPlaying);
                    break;

                case 'enter_tokyo_city':
                    $_dataDivAction = $this->getActionEnterTokyoCity($playerAction, $playerSessionIsPlaying);
                    break;

                case 'enter_tokyo_bay':
                    $_dataDivAction = $this->getActionEnterTokyoBay($playerAction, $playerSessionIsPlaying);
                    break;

                case 'has_won_by_killing':
                    $_dataDivAction = $this->getActionHasWonByKilling($playerAction, $playerSessionIsPlaying);
                    break;

                case 'has_won_by_victory_points':
                    $_dataDivAction = $this->getActionHasWonByVictoryPoints($playerAction, $playerSessionIsPlaying);
                    break;

                default:
                    $_dataDivAction['playBody'] = $_dataAction['nameAction'].' ACTION A CREER';
                    if ($playerSessionIsPlaying) {
                        $_dataDivAction['playBtn'] = 'OK';
                    } else {
                        $_dataDivAction['playBtn'] = null;
                    }
                    break;
            }
        } else {
            $_dataAction['nameAction'] = '';
            $_dataDivAction['playBody'] = '';
            $_dataDivAction['playBtn'] = null;
        }

        $_dataReturn = array_merge($_dataAction, $_dataDivAction);

        return $_dataReturn;
    }

    public function getActionHasWonByVictoryPoints(Player $playerAction, $playerSessionIsPlaying)
    {
        $_dataDivAction['playBody'] = ($playerSessionIsPlaying) ? 'Tu' : $playerAction->getName();
        $s = ($playerSessionIsPlaying) ? 's' : '';
        $_dataDivAction['playBody'] .= " a$s gagné !";

        $_dataDivAction['playBtn'] = null;
        $_dataDivAction['playBtn2'] = null;

        return $_dataDivAction;
    }

    public function getActionHasWonByKilling(Player $playerAction, $playerSessionIsPlaying)
    {
        $_dataDivAction['playBody'] = ($playerSessionIsPlaying) ? 'Tu' : $playerAction->getName();
        $s = ($playerSessionIsPlaying) ? 's' : '';
        $_dataDivAction['playBody'] .= " a$s gagné !";

        $_dataDivAction['playBtn'] = null;
        $_dataDivAction['playBtn2'] = null;

        return $_dataDivAction;
    }

    public function getActionAreDead(Player $playerAction, $playerSessionIsPlaying)
    {
        $_dataDivAction['playBody'] = '';
        $_deadPlayers = [];

        $_logToDo = $this->gamePlayService->getLogActionTodo($playerAction->getGame()->getId());
        if (0 !== count($_logToDo)) {
            $logToDo = $_logToDo[0];

            $_gameDataUpdates = $logToDo->getGameDataUpdates();
            foreach ($_gameDataUpdates as $gdu) {
                if ('dead' === $gdu->getType()) {
                    $_deadPlayers[] = $gdu->getPlayer();
                }
            }
        }

        if (0 === $playerAction->getInCity()) {
            $orientation = 'to-right';
        } else {
            $orientation = 'to-left';
        }

        foreach ($_deadPlayers as $deadPlayer) {
            $_dataDivAction['playBody'] .= '<p>'.$deadPlayer->getName().' ##monsters/'.$orientation.'/regular/'.$deadPlayer->getMonster()->getImgName().'££ est #/symbols/dead.png£ !</p>';
        }

        if ($playerSessionIsPlaying) {
            $_dataDivAction['playBtn'] = 'OK';
        } else {
            $_dataDivAction['playBtn'] = null;
        }
        $_dataDivAction['playBtn2'] = null;

        return $_dataDivAction;
    }

    public function getActionStartTurn(Player $playerAction, $playerSessionIsPlaying)
    {
        if ($playerSessionIsPlaying) {
            $_dataDivAction['playBody'] = "C'est à toi de jouer !";
            $_dataDivAction['playBtn'] = 'OK';
        } else {
            $_dataDivAction['playBody'] = 'En attente de '.$playerAction->getName();
            $_dataDivAction['playBtn'] = null;
        }
        $_dataDivAction['playBtn2'] = null;

        return $_dataDivAction;
    }

    public function getActionIsStartingInCity(Player $playerAction, $playerSessionIsPlaying)
    {
        $victoryPoints = $this->gamePlayService->getVictoryPointsIfInTokyo($playerAction);
        if ($playerSessionIsPlaying) {
            if (0 !== $playerAction->getInCity()) {
                $_dataDivAction['playBody'] = "Tu commences ton tour dans Tokyo et gagnes $victoryPoints#symbols/star.png£";
            } else {
                $_dataDivAction['playBody'] = 'Tu commences ton tour hors de Tokyo et ne gagnes pas de #symbols/star.png£';
            }
            $_dataDivAction['playBtn'] = 'Lancer les dés';
        } else {
            if (0 !== $playerAction->getInCity()) {
                $_dataDivAction['playBody'] = $playerAction->getName()." commence son tour dans Tokyo et gagne $victoryPoints#symbols/star.png£";
            } else {
                $_dataDivAction['playBody'] = $playerAction->getName().' commence son tour hors de Tokyo et ne gagne pas de #symbols/star.png£';
            }
            $_dataDivAction['playBtn'] = null;
        }
        $_dataDivAction['playBtn2'] = null;

        return $_dataDivAction;
    }

    /*public function getActionInitDices(Player $playerAction, $playerSessionIsPlaying)
    {
        $_dataDivAction['playBody'] = "<div class='row'><div class='col-12' id='playTitle'></div>";
        for ($i = 1; $i <= $playerAction->getNbDices(); ++$i) {
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
                    $faceDice = 'paw';
                    break;
                case 5:
                case 8:
                    $faceDice = 'heart';
                    break;
                case 6:
                    $faceDice = 'flash';
                    break;
            }

            $_dataDivAction['playBody'] .= "<div class='col m-0 p-1'><img class='img-fluid' src='/img/dices/$diceColor/dice_$faceDice.png'></div>";
        }
        $_dataDivAction['playBody'] .= '</div>';

        if ($playerSessionIsPlaying) {
            $_dataDivAction['playBtn'] = 'Lancer les dés';
        } else {
            $_dataDivAction['playBtn'] = null;
        }

        return $_dataDivAction;
    }*/

    public function getActionThrowDices(Player $playerAction, $playerSessionIsPlaying)
    {
        $_dicesTmp = $playerAction->getThrowTokyoDices();

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
        $_dices_kept = [];
        $_dices_new = [];
        foreach ($_dicesTmp as $dice) {
            if (($dice->getIsKept())) {
                $_dices_kept[$i]['typeOrder'] = $orderType[$dice->getType()];
                $_dices_kept[$i]['faceOrder'] = $oderFace[$dice->getFace()];
                $_dices_kept[$i]['obj'] = $dice;
            } else {
                $_dices_new[$i]['position'] = $dice->getPosition();
                $_dices_new[$i]['obj'] = $dice;
            }
            ++$i;
        }

        $typeOrder = array_column($_dices_kept, 'typeOrder');
        $faceOrder = array_column($_dices_kept, 'faceOrder');
        array_multisort($typeOrder, SORT_ASC, $faceOrder, SORT_ASC, $_dices_kept);

        $position = array_column($_dices_new, 'position');
        array_multisort($position, SORT_ASC, $_dices_new);

        $_dices = array_merge($_dices_kept, $_dices_new);

        $_dataDivAction['playBody'] = "<div class='row'><div class='col-12' id='playTitle'>";
        if ($playerSessionIsPlaying) {
            if ($playerAction->getThrowsLeft() > 0) {
                $_dataDivAction['playBody'] .= 'Choisis les dés à conserver';
            } else {
                $_dataDivAction['playBody'] .= 'Dernier lancer';
            }
        } else {
            $_dataDivAction['playBody'] .= $playerAction->getName().' a obtenu';
        }
        $_dataDivAction['playBody'] .= '</div>';

        foreach ($_dices as $_dice) {
            $dice = $_dice['obj'];
            $pos = $dice->getPosition();
            $diceColor = ($pos <= 6) ? 'black' : 'green';
            $_dataDivAction['playBody'] .= "<div id='dice_$pos' class='col m-0 p-1'>";
            $_dataDivAction['playBody'] .= "<img class='dice_img img-fluid' src='/img/dices/$diceColor/dice_".$dice->getFace().".png' data-position='$pos'>";
            $_dataDivAction['playBody'] .= "<div class='card-img-overlay img-fluid p-1 m-0' >";
            $_dataDivAction['playBody'] .= "<img id='lock_$pos' class='img-fluid' src='/img/dices/lock.png' style='display:";
            if (true == $dice->getIsKept()) {
                $_dataDivAction['playBody'] .= 'block';
            } else {
                $_dataDivAction['playBody'] .= 'none';
            }
            $_dataDivAction['playBody'] .= ";'  >";
            $_dataDivAction['playBody'] .= '</div>';
            $_dataDivAction['playBody'] .= "<div id='diceGif_$pos' class='card-img-overlay img-fluid p-1 m-0' >";
            if (false === $dice->getIsKept()) {
                $_dataDivAction['playBody'] .= "<img id='diceGifImg_$pos' class='img-fluid' src='/img/dices/$diceColor/dice.gif'>";
            }
            $_dataDivAction['playBody'] .= '</div>';

            if ($playerSessionIsPlaying && $playerAction->getThrowsLeft() > 0) {
                $_dataDivAction['playBody'] .= '<div id="diceCheckbox_'.$pos.'" class="mt-1" style="display:none;"><label class="switch_2 m-0">';
                $_dataDivAction['playBody'] .= '<input class="dice_checkbox" type="checkbox" data-position="'.$pos.'"';
                if (true == $dice->getIsKept()) {
                    $_dataDivAction['playBody'] .= ' checked';
                }
                $_dataDivAction['playBody'] .= '>';
                $_dataDivAction['playBody'] .= '<span class="slider_2 round"></span>';
                $_dataDivAction['playBody'] .= '</label></div>';
            }

            $_dataDivAction['playBody'] .= '</div>';
        }
        $s = ($playerAction->getThrowsLeft() > 1) ? 's' : '';

        $_dataDivAction['playBody'] .= '<div class=\'col-12\' id=\'playThrowsLeft\'>';
        if ($playerAction->getThrowsLeft() > 0) {
            $_dataDivAction['playBody'] .= '<span id="throwsLeft">'.$playerAction->getThrowsLeft()."</span> Lancer$s Restant$s";
        }
        $_dataDivAction['playBody'] .= '</div>';
        $_dataDivAction['playBody'] .= '</div>';

        if ($playerSessionIsPlaying) {
            if ($playerAction->getThrowsLeft() > 0) {
                $_dataDivAction['playBtn'] = 'Relancer les dés';
            } else {
                $_dataDivAction['playBtn'] = 'Résoudre les dés';
            }
        } else {
            $_dataDivAction['playBtn'] = null;
        }
        $_dataDivAction['playBtn2'] = null;

        return $_dataDivAction;
    }

    public function getActionResolveOrderDices(Player $playerAction, $playerSessionIsPlaying)
    {
        $_dicesByType['victory']['one'] = 0;
        $_dicesByType['victory']['two'] = 0;
        $_dicesByType['victory']['three'] = 0;
        $_dicesByType['heart']['heart'] = 0;
        $_dicesByType['flash']['flash'] = 0;
        $_dicesByType['paw']['paw'] = 0;

        $label['victory'] = 'star';
        $label['heart'] = 'heart';
        $label['flash'] = 'flash';
        $label['paw'] = 'paw';

        $_nb['victory'] = 0;
        $_nb['heart'] = 0;
        $_nb['flash'] = 0;
        $_nb['paw'] = 0;

        $_dices = $playerAction->getThrowTokyoDices();
        foreach ($_dices as $dice) {
            ++$_dicesByType[$dice->getType()][$dice->getFace()];
            ++$_nb[$dice->getType()];
        }

        $idRow = 1;
        $_dataDivAction['playBody'] = "<div class='col-12 m-0 p-0'>";
        if ($playerSessionIsPlaying) {
            $_dataDivAction['playBody'] .= "Choisis l'ordre de résolution";
        } else {
            $_dataDivAction['playBody'] .= $playerAction->getName().' a obtenu';
        }
        $_dataDivAction['playBody'] .= '</div>';

        $totalOccupied = max($_nb) + 4;
        $colsEmpty = 12 - $totalOccupied;
        $colLeft = ceil($colsEmpty / 2);
        $colRight = floor($colsEmpty / 2);

        foreach ($_dicesByType as $type => $faces) {
            $nbDices = 0;
            foreach ($faces as $face => $nb) {
                for ($i = 0; $i < $nb; ++$i) {
                    ++$nbDices;
                }
            }
            if (0 === $nbDices) {
                continue;
            }

            $_dataDivAction['playBody'] .= "<div class='row p-0 m-0 text-center'>";

            $_dataDivAction['playBody'] .= "<div class='col-$colLeft m-0 p-0'></div>";

            $_dataDivAction['playBody'] .= "<div id='resolve_1_$idRow' class='col-1 p-0 m-0 mr-3' ><img id='img_".$label[$type]."' class='img-fluid' src='/img/symbols/".$label[$type].".png' data-typedice='$type'></div>";

            $nbColsOccupied = 0;
            foreach ($faces as $face => $nb) {
                for ($i = 0; $i < $nb; ++$i) {
                    $_dataDivAction['playBody'] .= '<div id="resolve_'.($nbColsOccupied + 2).'_'.$idRow.'" class="col-1 m-0 pad-1" ><img class="img-fluid" src="/img/dices/black/dice_'.$face.'.png" style="vertical-align:middle;"></div>';
                    ++$nbColsOccupied;
                }
            }

            if (0 === $nbColsOccupied) {
                if (!$playerSessionIsPlaying) {
                    $_dataDivAction['playBody'] .= '<div class="col-'.max($_nb).' pad-1 m-0" >Aucun dé</div>';
                    $nbColsOccupied = max($_nb);
                }
            }

            for ($i = 0; $i < max($_nb) - $nbColsOccupied; ++$i) {
                $_dataDivAction['playBody'] .= '<div id="resolve_'.($i + 2 + $nbColsOccupied).'_'.$idRow.'" class="col-1 pad-1 m-0" ></div>';
            }

            $_dataDivAction['playBody'] .= "<div class='col-1 m-0 pad-1'>";

            if ($playerSessionIsPlaying) {
                $_dataDivAction['playBody'] .= "<button  class='btn btn-danger btn_arrow_top m-0 p-0 ml-2' data-position='$idRow' ";
                if (1 === $idRow) {
                    $_dataDivAction['playBody'] .= 'disabled';
                }
                $_dataDivAction['playBody'] .= "><img class='img-fluid img_arrow  p-0 m-0' src='/img/icons/arrow-top.png' ></button>";
            }

            $_dataDivAction['playBody'] .= '</div>';

            $_dataDivAction['playBody'] .= "<div class='col-1 m-0 pad-1'>";

            if ($playerSessionIsPlaying) {
                $_dataDivAction['playBody'] .= "<button  class='btn btn-danger btn_arrow_bottom m-0 p-0 ml-2' data-position='$idRow' ";
                if (4 === $idRow) {
                    $_dataDivAction['playBody'] .= 'disabled';
                }
                $_dataDivAction['playBody'] .= "><img class='img-fluid img_arrow  p-0 m-0' src='/img/icons/arrow-bottom.png'></button>";
            }

            $_dataDivAction['playBody'] .= '</div>';
            $_dataDivAction['playBody'] .= "<div class='col-$colRight m-0 p-0'></div>";
            $_dataDivAction['playBody'] .= '</div>';

            ++$idRow;
        }

        if ($playerSessionIsPlaying) {
            $_dataDivAction['playBtn'] = 'Résoudre les dés';
        } else {
            $_dataDivAction['playBtn'] = null;
        }
        $_dataDivAction['playBtn2'] = null;

        return $_dataDivAction;
    }

    public function getActionResolveDicesPaws(Player $playerAction, $playerSessionIsPlaying)
    {
        $nbDicesPaw = $this->gamePlayService->getNbDicesPaws($playerAction);

        $_dataDivAction['playBody'] = '';

        $_playersAttacked = [];

        if (0 === $nbDicesPaw) {
            $s = ($playerSessionIsPlaying) ? 's' : '';
            $xt = ($playerSessionIsPlaying) ? 'x' : 't';
            if ($playerSessionIsPlaying) {
                $_dataDivAction['playBody'] .= 'Tu';
            } else {
                $_dataDivAction['playBody'] .= $playerAction->getName();
            }
            $_dataDivAction['playBody'] .= " n'a$s obtenu aucun #dices/black/dice_paw.png£ et ne peu$xt donc pas attaquer";
        } else {
            $_playersEnemy = $playerAction->getGame()->getPlayers();
            if (0 === $playerAction->getInCity()) {
                foreach ($_playersEnemy as $playerEnemy) {
                    if ($playerEnemy != $playerAction && 0 !== $playerEnemy->getInCity() && true === $playerEnemy->getIsAlive()) {
                        $_playersAttacked[] = $playerEnemy->getName().' ##monsters/to-right/regular/'.$playerEnemy->getMonster()->getImgName().'££';
                    }
                }
            } else {
                foreach ($_playersEnemy as $playerEnemy) {
                    if ($playerEnemy != $playerAction && 0 === $playerEnemy->getInCity() && true === $playerEnemy->getIsAlive()) {
                        $_playersAttacked[] = $playerEnemy->getName().' ##monsters/to-left/regular/'.$playerEnemy->getMonster()->getImgName().'££';
                    }
                }
            }

            if (count($_playersAttacked) > 0) {
                if ($playerSessionIsPlaying) {
                    $_dataDivAction['playBody'] .= '<p>Tu as attaqué</p>';
                } else {
                    $_dataDivAction['playBody'] .= $playerAction->getName().' a attaqué';
                }

                if (0 === $playerAction->getInCity()) {
                    $orientation = 'to-right';
                    $location = 'dans Tokyo';
                } else {
                    $orientation = 'to-left';
                    $location = 'hors de Tokyo';
                }

                $_dataDivAction['playBody'] .= '<p>';
                $stringPlayersAttacked = implode(', ', $_playersAttacked);
                $_dataDivAction['playBody'] .= $stringPlayersAttacked;
                $_dataDivAction['playBody'] .= '</p>';

                $_dataDivAction['playBody'] .= "<p>$location avec ";
                for ($i = 1; $i <= $this->gamePlayService->getPawsByDices($playerAction); ++$i) {
                    $_dataDivAction['playBody'] .= '#dices/black/dice_paw.png£';
                }
                $_dataDivAction['playBody'] .= '</p>';
            } else {
                $_dataDivAction['playBody'] .= "Personne n'est dans Tokyo, ";
                if ($playerSessionIsPlaying) {
                    $_dataDivAction['playBody'] .= 'tu ne peux ';
                } else {
                    $_dataDivAction['playBody'] .= $playerAction->getName().' ne peut ';
                }
                $_dataDivAction['playBody'] .= 'donc pas attaquer';
            }
        }

        if ($playerSessionIsPlaying) {
            $_dataDivAction['playBtn'] = 'OK';
        } else {
            $_dataDivAction['playBtn'] = null;
        }
        $_dataDivAction['playBtn2'] = null;

        return $_dataDivAction;
    }

    public function getActionResolveDicesMana(Player $playerAction, $playerSessionIsPlaying)
    {
        $nbDicesMana = $this->gamePlayService->getNbDicesMana($playerAction);

        if ($playerSessionIsPlaying) {
            $_dataDivAction['playBody'] = 'Tu';
            if ($nbDicesMana > 0) {
                $_dataDivAction['playBody'] .= ' as gagné '.$this->gamePlayService->getManaByDices($playerAction).'#symbols/flash.png£ avec ';
                for ($i = 0; $i < $nbDicesMana; ++$i) {
                    $_dataDivAction['playBody'] .= '#dices/black/dice_flash.png£';
                }
            } else {
                $_dataDivAction['playBody'] .= " n'as obtenu aucun #dices/black/dice_flash.png£ et ne gagne donc pas de #symbols/flash.png£";
            }

            $_dataDivAction['playBtn'] = 'OK';
        } else {
            $_dataDivAction['playBody'] = $playerAction->getName();
            if ($nbDicesMana > 0) {
                $_dataDivAction['playBody'] .= ' a gagné '.$this->gamePlayService->getManaByDices($playerAction).'#symbols/flash.png£ avec';
                for ($i = 0; $i < $nbDicesMana; ++$i) {
                    $_dataDivAction['playBody'] .= '#dices/black/dice_flash.png£';
                }
            } else {
                $_dataDivAction['playBody'] .= "n'a obtenu aucun #dices/black/dice_flash.png£ et ne gagne donc pas de #symbols/flash.png£";
            }

            $_dataDivAction['playBtn'] = null;
        }

        $_dataDivAction['playBtn2'] = null;

        return $_dataDivAction;
    }

    public function getActionEnterTokyoCity(Player $playerAction, $playerSessionIsPlaying)
    {
        $playerLabel = ($playerSessionIsPlaying) ? 'Tu ' : $playerAction->getName().' ';
        $st = ($playerSessionIsPlaying) ? 's' : 't';
        $s = ($playerSessionIsPlaying) ? 's' : '';
        $_dataDivAction['playBody'] = "Tokyo City est inoccupée, $playerLabel doi$st donc y entrer et gagne$s 1#symbols/star.png£";

        $_dataDivAction['playBtn'] = ($playerSessionIsPlaying) ? 'OK' : null;
        $_dataDivAction['playBtn2'] = null;

        return $_dataDivAction;
    }

    public function getActionEnterTokyoBay(Player $playerAction, $playerSessionIsPlaying)
    {
        $playerLabel = ($playerSessionIsPlaying) ? 'Tu ' : $playerAction->getName().' ';
        $st = ($playerSessionIsPlaying) ? 's' : 't';
        $s = ($playerSessionIsPlaying) ? 's' : '';
        $_dataDivAction['playBody'] = "Tokyo Bay est inoccupée, $playerLabel doi$st donc y entrer et gagne$s 1#symbols/star.png£";

        $_dataDivAction['playBtn'] = ($playerSessionIsPlaying) ? 'OK' : null;
        $_dataDivAction['playBtn2'] = null;

        return $_dataDivAction;
    }

    public function getActionResolveDicesHeart(Player $playerAction, $playerSessionIsPlaying)
    {
        $nbDicesHeart = $this->gamePlayService->getNbDicesHeart($playerAction);

        $_dataDivAction['playBody'] = ($playerSessionIsPlaying) ? 'Tu ' : $playerAction->getName().' ';
        $s = ($playerSessionIsPlaying) ? 's' : '';
        $t = ($playerSessionIsPlaying) ? '' : 't';
        $xt = ($playerSessionIsPlaying) ? 'x' : 't';

        if (0 == $nbDicesHeart) {
            $_dataDivAction['playBody'] .= "n'a$s aucun #dices/black/dice_heart.png£ et n'a$s donc pas gagné de #symbols/heart.png£";
        } elseif (0 !== $playerAction->getInCity()) {
            $_dataDivAction['playBody'] .= "es$t dans Tokyo et ne peu$xt donc pas gagner de #symbols/heart.png£";
        } else {
            $_dataDivAction['playBody'] .= " a$s gagné ".$this->gamePlayService->getHealthPointsByDices($playerAction).'#symbols/heart.png£ avec ';
            for ($i = 0; $i < $this->gamePlayService->getNbDicesHeart($playerAction); ++$i) {
                $_dataDivAction['playBody'] .= '#dices/black/dice_heart.png£';
            }
        }
        $_dataDivAction['playBtn'] = ($playerSessionIsPlaying) ? 'OK' : null;
        $_dataDivAction['playBtn2'] = null;

        return $_dataDivAction;
    }

    public function getActionResolveDicesVictory(Player $playerAction, $playerSessionIsPlaying)
    {
        $_dicesVictory = $this->gamePlayService->getNbDicesVictory($playerAction);
        $nbDicesVictory = array_sum($_dicesVictory);

        $_dataDivAction['playBody'] = ($playerSessionIsPlaying) ? 'Tu ' : $playerAction->getName().' ';
        $s = ($playerSessionIsPlaying) ? 's' : '';
        $t = ($playerSessionIsPlaying) ? '' : 't';
        $xt = ($playerSessionIsPlaying) ? 'x' : 't';

        if (0 == $nbDicesVictory) {
            $_dataDivAction['playBody'] .= "n'a$s aucun #dices/black/dice_one.png£ #dices/black/dice_two.png£ #dices/black/dice_three.png£ et n'a$s donc pas gagné de #symbols/star.png£";
        } else {
            $_victoryPoints['one'] = 1;
            $_victoryPoints['two'] = 2;
            $_victoryPoints['three'] = 3;

            $_dataDivAction['playBody'] .= " a$s gagné ".$this->gamePlayService->getVictoryPointsByDices($playerAction).'#symbols/star.png£ ';
            foreach ($_dicesVictory as $face => $nb) {
                if (0 != $nb) {
                    $_dataDivAction['playBody'] .= '<div>';
                    for ($i = 0; $i < $nb; ++$i) {
                        $_dataDivAction['playBody'] .= "#dices/black/dice_$face.png£";
                    }
                    if ($nb >= 3) {
                        $victoryPoints = $_victoryPoints[$face] + ($nb - 3);
                    } else {
                        $victoryPoints = 0;
                    }

                    $_dataDivAction['playBody'] .= ' = '.$victoryPoints.'#symbols/star.png£';
                    $_dataDivAction['playBody'] .= '</div>';
                }
            }
        }

        if ($playerSessionIsPlaying) {
            $_dataDivAction['playBtn'] = 'OK';
        } else {
            $_dataDivAction['playBtn'] = null;
        }

        $_dataDivAction['playBtn2'] = null;

        return $_dataDivAction;
    }

    public function getActionAskToLeaveTokyo($playerAction, $playerSessionIsPlaying)
    {
        if ($playerSessionIsPlaying) {
            $_dataDivAction['playBody'] = '<p>Tu as été attaqué</p>';
            $_dataDivAction['playBody'] .= '<p>Choisis de rester ou de fuir Tokyo</p>';
            $_dataDivAction['playBtn'] = 'Rester';
            $_dataDivAction['playBtn2'] = 'Fuir';
        } else {
            $_dataDivAction['playBody'] = $playerAction->getName().' a été attaqué et doit choisir de rester ou de fuir Tokyo';
            $_dataDivAction['playBtn'] = null;
            $_dataDivAction['playBtn2'] = null;
        }

        return $_dataDivAction;
    }
}
