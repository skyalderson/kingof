<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Player;
use App\Form\GameType;
use App\Form\LobbyType;
use App\Repository\BoardRepository;
use App\Repository\GameRepository;
use App\Repository\ModeRepository;
use App\Repository\MonsterRepository;
use App\Repository\RuleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class GameController extends AbstractController
{
    /**
     * @Route("/game/create", name="game.create")
     * @isGranted("ROLE_USER")
     *
     * @param ModeRepository $modeRepo
     * @param BoardRepository $boardRepo
     * @param MonsterRepository $monsterRepo
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function gameCreate(ModeRepository $modeRepo, BoardRepository $boardRepo, MonsterRepository $monsterRepo)
    {
        $em = $this->getDoctrine()->getManager();

        $game = new Game();
        $game->setName('Partie de '.$this->getUser()->getUsername());

        $mode = $modeRepo->find(1);
        $game->setMode($mode);

        $board = $boardRepo->find(1);
        $game->setBoard($board);

        $monsters = $monsterRepo->findAll();
        foreach ($monsters as $monster) {
            if($monster->getAvailable() === true) $game->addMonstersAuthorized($monster);
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
     * @param Request $request
     * @param RuleRepository $ruleRepo
     * @return RedirectResponse|Response
     *
     * @throws \Exception
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
     * @param GameRepository $GameRepository
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
     * @param Game $game
     * @return Response
     *
     * @throws \Exception
     */
    public function gameLobby(Game $game)
    {
        // TODO : TESTER NB JOUEURS MAX

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
     * @param Game $game
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
     * @param Player $player
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
     * @param Player $player
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
}
