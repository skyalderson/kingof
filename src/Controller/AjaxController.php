<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\MonsterRepository;
use App\Repository\PlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    /**
     * @Route("/lobby/launch", name="lobby.launch", methods={"POST"})
     *
     * @param Request $req
     * @param GameRepository $gameRepo
     * @param PlayerRepository $playerRepo
     * @return Response
     */
    public function launchGame(Request $req, GameRepository $gameRepo, PlayerRepository $playerRepo)
    {
        if ($req->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $idGame = $req->get('idGame');
            $game = $gameRepo->find($idGame);

            $game->setState(2);
            $em->persist($game);

            $_repoPlayers = $game->getPlayers()->toArray();

            for ($i = 1; $i <= count($_repoPlayers) ; $i++) {
                $rand[$i] = $i;
            }

            foreach ($_repoPlayers as $player)
            {
                $player = $playerRepo->find($player->getId());
                $turn = array_rand($rand, 1);
                unset($rand[$turn]);

                $player->setTurn($turn);
                $em->persist($player);
            }

            $em->flush();

            return new Response('OK');
        } else {
            return new Response('ERROR', 400);
        }
    }

    /**
     * @Route("/lobby/islaunchready", name="lobby.islaunchready", methods={"POST"})
     *
     * @param Request $req
     * @param GameRepository $gameRepo
     * @return JsonResponse|Response
     */
    public function isLaunchReady(Request $req, GameRepository $gameRepo)
    {
        if ($req->isXmlHttpRequest()) {
            $isLaunchPossible = true;

            $idGame = $req->get('idGame');
            $game = $gameRepo->find($idGame);

            if (null === $game) {
                $isLaunchPossible = false;
            } else {
                $_repoPlayers = $game->getPlayers()->toArray();
                foreach ($_repoPlayers as $p) {
                    if (false == $p->getIsReady() || null === $p->getMonster()) {
                        $isLaunchPossible = false;
                    }
                }
            }

            return new JsonResponse(['launch_possible' => json_encode($isLaunchPossible)]);
        } else {
            return new Response('ERROR', 400);
        }
    }

    /**
     * @Route("/lobby/data", name="lobby.data", methods={"POST"})
     *
     * @param Request $req
     * @param GameRepository $gameRepo
     * @return JsonResponse|Response
     */
    public function updateDataLobby(Request $req, GameRepository $gameRepo)
    {
        if ($req->isXmlHttpRequest()) {
            $idGame = $req->get('idGame');
            $idPlayer = $req->get('idPlayer');

            $isKicked = true;
            $isWaiting = true;
            $isLaunchPossible = true;
            $_players = [];

            $game = $gameRepo->find($idGame);
            if (null === $game) {
                $isGameExisting = false;
                $isLaunchPossible = false;
            } else {
                $isGameExisting = true;

                $isWaiting = (1 == $game->getState()) ? true : false;
                if ($isWaiting) {
                    $_repoPlayers = $game->getPlayers()->toArray();
                    foreach ($_repoPlayers as $p) {
                        if ($idPlayer != $p->getId()) {
                            $_players[$p->getId()] = [];
                            $_players[$p->getId()]['id'] = $p->getId();
                            $_players[$p->getId()]['monster'] = (null === $p->getMonster()) ? 'Aucun monstre sélectionné' : $p->getMonster()->getName();
                            $_players[$p->getId()]['ready'] = $p->getIsReady();
                            $_players[$p->getId()]['name'] = $p->getUser()->getUsername();
                        } else {
                            $isKicked = false;
                        }
                        // si au moins un des joueurs n'a pas choisi de monstre ou n'est pas prêt, la partie n'est pas débutable
                        if (false == $p->getIsReady() || null === $p->getMonster()) {
                            $isLaunchPossible = false;
                        }
                    }
                } else {
                    $isLaunchPossible = false;
                }
            }

            return new JsonResponse([
                'launch_possible' => json_encode($isLaunchPossible),
                'exists' => json_encode($isGameExisting),
                'waiting' => json_encode($isWaiting),
                'kicked' => json_encode($isKicked),
                'data' => json_encode($_players), ]);
        } else {
            return new Response('ERROR', 400);
        }
    }

    /**
     * @Route("/lobby/ready", name="lobby.ready", methods={"POST"})
     *
     * @param Request $req
     * @param PlayerRepository $playerRepo
     * @return Response
     */
    public function readyState(Request $req, PlayerRepository $playerRepo)
    {
        if ($req->isXmlHttpRequest()) {
            $idPlayer = $req->get('idPlayer');
            $readyTxt = $req->get('ready');

            $ready = ('true' == $readyTxt) ? true : false;

            $player = $playerRepo->find($idPlayer);
            $player->setIsReady($ready);

            $em = $this->getDoctrine()->getManager();
            $em->persist($player);
            $em->flush();

            return new Response('ok');
        } else {
            return new Response('ERROR', 400);
        }
    }

    /**
     * @Route("/lobby/select/monster", name="lobby.select.monster", methods={"POST"})
     *
     * @param Request $req
     * @param PlayerRepository $playerRepo
     * @param MonsterRepository $monsterRepo
     * @return Response
     */
    public function selectMonster(Request $req, PlayerRepository $playerRepo, MonsterRepository $monsterRepo)
    {
        if ($req->isXmlHttpRequest()) {
            $idGame = $req->get('idGame');
            $idPlayer = $req->get('idPlayer');
            $idMonster = $req->get('idMonster');

            $_players = $playerRepo->findMonstersWithoutMe($idGame, $idPlayer);

            $isTaken = false;
            foreach ($_players as $p) {
                if (null !== $p->getMonster() && $p->getMonster()->getId() == $idMonster) {
                    $isTaken = true;
                }
            }
            $em = $this->getDoctrine()->getManager();
            $player = $playerRepo->find($idPlayer);

            if (!$isTaken) {
                $monster = $monsterRepo->find($idMonster);
                $player->setMonster($monster);

                $em->persist($player);
                $em->flush();

                return new Response($idMonster);
            } else {
                $player->setMonster(null);
                $em->persist($player);
                $em->flush();

                return new Response('taken');
            }
            //return new JsonResponse(array("data" => json_encode($players)));
        } else {
            return new Response('ERROR', 400);
        }
    }

    /**
     * @Route("/list/data", name="list.data", methods={"POST"})
     *
     * @param Request $req
     * @param GameRepository $gameRepo
     * @return JsonResponse|Response
     */
    public function updateDataList(Request $req, GameRepository $gameRepo)
    {
        if ($req->isXmlHttpRequest()) {
            $_games_return = [];
            $games = $gameRepo->findByState(1);
            foreach ($games as $k => $game) {
                $_games_return[$k]['id'] = $game->getId();
                $_games_return[$k]['name'] = $game->getName();
                $_games_return[$k]['nb_players'] = $game->getNbPlayers();
                $_games_return[$k]['img_board'] = $game->getBoard()->getImgName();
                $_games_return[$k]['monster_select'] = $game->getMonstersSelectLabel();
                $_games_return[$k]['mode'] = $game->getMode()->getName();
                $_games_return[$k]['max_players'] = $game->getMaxPlayers();
            }

            return new JsonResponse(['games' => json_encode($_games_return)]);
        }

        return new Response('ERREUR', 400);
    }

    /**
     * @Route("/game/slot_available", name="game.slot_available", methods={"POST"})
     *
     * @param Request $req
     * @param GameRepository $gameRepo
     * @return Response
     */
    public function isSlotAvailable(Request $req, GameRepository $gameRepo)
    {
        if ($req->isXmlHttpRequest()) {
            $idGame = $req->get('idGame');
            $idUser = $req->get('idUser');

            $game = $gameRepo->find($idGame);

            if ($game->getNbPlayers() == $game->getMaxPlayers()) {
                $_players = $game->getPlayers()->toArray();
                $isHere = 'no';
                foreach ($_players as $player) {
                    if ($player->getUser()->getId() == $idUser) {
                        $isHere = 'ok';
                        break;
                    }
                }

                return new Response($isHere);
            } else {
                return new Response('ok');
            }
        }

        return new Response('ERROR', 400);
    }
}
