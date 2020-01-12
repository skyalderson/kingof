<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\KotCardGame;
use App\Entity\Log;
use App\Entity\Player;
use App\Form\GameType;
use App\Form\LobbyType;
use App\Repository\BoardRepository;
use App\Repository\GameRepository;
use App\Repository\KotCardRepository;
use App\Repository\ModeRepository;
use App\Repository\MonsterRepository;
use App\Repository\PlayerRepository;
use App\Repository\RuleRepository;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameCreationController extends AbstractController
{
    /**
     * @Route("/game/create", name="game.create")
     * @isGranted("ROLE_USER")
     *
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function gameCreate(ModeRepository $modeRepo, BoardRepository $boardRepo, MonsterRepository $monsterRepo)
    {
        $em = $this->getDoctrine()->getManager();

        $game = new Game();
        $game->setCreatedAt(new \DateTime('now'));
        $game->setName('Partie de '.$this->getUser()->getUsername());

        $mode = $modeRepo->find(1);
        $game->setMode($mode);

        $board = $boardRepo->find(1);
        $game->setBoard($board);

        $monsters = $monsterRepo->findAll();
        foreach ($monsters as $monster) {
            if (true === $monster->getAvailable()) {
                $game->addMonstersAuthorized($monster);
            }
        }

        $game->setState(1);
        $game->setMaxPlayers(6);

        $em->persist($game);
        $em->flush();

        $creator = new Player();
        $creator->setGame($game);
        $creator->setUser($this->getUser());
        $creator->setCreator(true);
        $creator->setJoinedAt(new \DateTime('now'));

        $em->persist($creator);
        $em->flush();

        return $this->redirectToRoute('game.lobby', ['id' => $game->getId()]);
    }

    /**
     * @Route("/game/create/advanced", name="game.create.advanced")
     * @isGranted("ROLE_USER")
     *
     * @return RedirectResponse|Response
     *
     * @throws Exception
     */
    public function gameCreateAdvanded(Request $request, RuleRepository $ruleRepo)
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $this->isCsrfTokenValid('create-game', $request->request->get('token'))) {
            $em = $this->getDoctrine()->getManager();

            $game->setState(1);

            $creator = new Player();
            $creator->setGame($game);
            $creator->setUser($this->getUser());
            $creator->setCreator(true);
            $creator->setIsReady(false);
            $creator->setJoinedAt(new \DateTime('now'));

            $em->persist($game);
            $em->persist($creator);
            $em->flush();

            return $this->redirectToRoute('game.lobby', ['id' => $game->getId()]);
        }

        $_imgRulesTmp = $ruleRepo->findAllImg();
        foreach ($_imgRulesTmp as $v) {
            $_imgRules[$v['id']] = $v['imgName'];
        }

        return $this->render('game/creation.html.twig', [
            'form' => $form->createView(),
            'imgRules' => $_imgRules,
        ]);
    }

    /**
     * @Route("/game/list", name="game.list")
     * @isGranted("ROLE_USER")
     *
     * @return Response
     */
    public function gameList(GameRepository $GameRepository)
    {
        $_games = $GameRepository->findByState(1);

        return $this->render('game/list.html.twig', [
            'games' => $_games,
        ]);
    }

    /**
     * @Route("/game/lobby/{id}", name="game.lobby", methods={"GET"})
     * @isGranted("ROLE_USER")
     *
     * @return Response
     *
     * @throws Exception
     */
    public function gameLobby(Game $game)
    {
        $isAbsent = true;
        foreach ($game->getPlayers() as $p) {
            if ($p->getUser() == $this->getUser()) {
                $player = $p;
                $isAbsent = false;
            }
        }
        if ($isAbsent) {
            $player = new Player();
            $player->setGame($game);
            $player->setUser($this->getUser());
            $player->setCreator(false);
            $player->setIsReady(false);
            $player->setJoinedAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($player);
            $game->addPlayer($player);
            $em->persist($game);
            $em->flush();
        }
        $form = $this->createForm(LobbyType::class, ['game' => $game]);

        return $this->render('game/lobby.html.twig', [
            'game' => $game,
            'player' => $player,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/game/delete/{id}", name="game.delete", methods={"GET"})
     * @isGranted("ROLE_USER")
     *
     * @return RedirectResponse
     */
    public function gameDelete(Game $game)
    {
        $this->denyAccessUnlessGranted('delete', $game);
        $em = $this->getDoctrine()->getManager();
        $em->remove($game);
        $em->flush();

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/player/kick/{id}", name="player.kick", methods={"GET"})
     * @isGranted("ROLE_USER")
     *
     * @return RedirectResponse
     */
    public function gameKick(Player $player)
    {
        $this->denyAccessUnlessGranted('kick', $player);
        $idGame = $player->getGame()->getId();

        $em = $this->getDoctrine()->getManager();
        $em->remove($player);
        $em->flush();

        return $this->redirectToRoute('game.lobby', ['id' => $idGame]);
    }

    /**
     * @Route("/game/quit/{id}", name="game.quit", methods={"GET"})
     * @isGranted("ROLE_USER")
     *
     * @return RedirectResponse
     */
    public function gameQuit(Player $player)
    {
        $this->denyAccessUnlessGranted('quit', $player);

        $em = $this->getDoctrine()->getManager();
        $em->remove($player);
        $em->flush();

        return $this->redirectToRoute('game.list');
    }

    /**
     * @Route("/lobby/launch", name="lobby.launch", methods={"POST"})
     *
     * @return Response
     *
     * @throws Exception
     */
    public function launchGame(Request $req, GameRepository $gameRepo, PlayerRepository $playerRepo, KotCardRepository $kotCardRepo)
    {
        if ($req->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $idGame = $req->get('idGame');
            $game = $gameRepo->find($idGame);
            $game->setState(2);
            $game->setStartedAt(new \DateTime('now'));

            $log = new Log();
            $log->setGame($game);

            $_repoPlayers = $game->getPlayers()->toArray();
            for ($i = 1; $i <= count($_repoPlayers); ++$i) {
                $rand[$i] = $i;
            }
            foreach ($_repoPlayers as $player) {
                $player = $playerRepo->find($player->getId());
                $turn = array_rand($rand, 1);
                unset($rand[$turn]);

                $player->setTurn($turn);

                if (1 === $turn) {
                    $player->setIsPlaying(true);
                    $log->setPlayer($player);
                }
                $em->persist($player);
            }

            $log->setIsDone(false);
            $log->setAction('start_turn');
            $em->persist($log);

            $_cards = $kotCardRepo->findAll();
            $i = 0;
            foreach ($_cards as $card) {
                if (true === $card->getAvailable()) {
                    $_cardGame[$i] = new KotCardGame();
                    $_cardGame[$i]->setGame($game);
                    $_cardGame[$i]->setKotCard($card);
                    $_cardGame[$i]->setState('pioche');
                    $em->persist($_cardGame[$i]);
                    ++$i;

                    if (15 === $card->getId() || 62 === $card->getId()) {
                        $_cardGame[$i] = new KotCardGame();
                        $_cardGame[$i]->setGame($game);
                        $_cardGame[$i]->setKotCard($card);
                        $_cardGame[$i]->setState('pioche');
                        $em->persist($_cardGame[$i]);
                        ++$i;
                    }
                }
            }
            $em->flush();

            for ($j = 0; $j < count($_cardGame); ++$j) {
                $randCard[$j] = $j;
            }

            $_randomCards = array_rand($_cardGame, 3);
            $k = 1;
            foreach ($_randomCards as $randomCard) {
                $_cardGame[$randomCard]->setState('achat');
                $_cardGame[$randomCard]->setPosition($k);
                ++$k;
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
