<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\GamePlayService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $playService;

    public function __construct(GamePlayService $gamePlayService)
    {
        $this->gamePlayService = $gamePlayService;
    }

    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', []);
    }

    /**
     * @Route("/rules", name="rules")
     */
    public function rules()
    {
        return $this->render('rules/index.html.twig', []);
    }

    /**
     * @Route("/rankings", name="rankings")
     */
    public function rankings(UserRepository $userRepo)
    {
        $_users = $userRepo->findAll();
        foreach ($_users as $user) {
            $_players[$user->getId()] = $user->getPlayers()->toArray();
            $ranking[$user->getId()]['pseudo'] = $user->getUsername();
            $ranking[$user->getId()]['nbGamesPlayed'] = 0;
            $ranking[$user->getId()]['nbGamesWon'] = 0;
            $ranking[$user->getId()]['nbGamesWonKills'] = 0;
            $ranking[$user->getId()]['nbGamesWonPoints'] = 0;
            foreach ($_players[$user->getId()] as $player) {
                if (3 === $player->getGame()->getState()) {
                    ++$ranking[$user->getId()]['nbGamesPlayed'];
                    if (true === $player->getIsWinner()) {
                        ++$ranking[$user->getId()]['nbGamesWon'];
                        if ('kills' == $player->getGame()->getVictoryType()) {
                            ++$ranking[$user->getId()]['nbGamesWonKills'];
                        } elseif ('points' == $player->getGame()->getVictoryType()) {
                            ++$ranking[$user->getId()]['nbGamesWonPoints'];
                        }
                    }
                }
            }
            $ranking[$user->getId()]['prctWin'] = (0 !== $ranking[$user->getId()]['nbGamesPlayed']) ? 100 * $ranking[$user->getId()]['nbGamesWon'] / $ranking[$user->getId()]['nbGamesPlayed'] : 0;
            $ranking[$user->getId()]['prctWinKills'] = (0 !== $ranking[$user->getId()]['nbGamesPlayed']) ? 100 * $ranking[$user->getId()]['nbGamesWonKills'] / $ranking[$user->getId()]['nbGamesPlayed'] : 0;
            $ranking[$user->getId()]['prctWinPoints'] = (0 !== $ranking[$user->getId()]['nbGamesPlayed']) ? 100 * $ranking[$user->getId()]['nbGamesWonPoints'] / $ranking[$user->getId()]['nbGamesPlayed'] : 0;

        }

        $nbGamesWon = array_column($ranking, 'nbGamesWon');
        $prctWin = array_column($ranking, 'prctWin');
        $nbGamesPlayed = array_column($ranking, 'nbGamesPlayed');
        array_multisort($nbGamesWon, SORT_DESC, $prctWin, SORT_DESC, $nbGamesPlayed, SORT_DESC, $ranking);

        return $this->render('rankings/index.html.twig', [
            'ranking' => $ranking,
            ]);
    }
}
