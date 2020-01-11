<?php

namespace App\Controller;

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
        /*$idGame = $this->playService->hasGame($this->getUser());
        if (0 != $idGame) {
            $this->redirectToRoute('play', $idGame);
        } else {*/
            return $this->render('home/index.html.twig', [

        ]);
        //}
    }
}
