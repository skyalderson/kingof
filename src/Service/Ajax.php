<?php

namespace App\Service;

use App\Repository\PlayerRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class Ajax
{
    private $playerRepo;
    
    public function __construct(PlayerRepository $playerRepo)
    {
        $this->$playerRepo = $playerRepo;   
    }
    
    /**
     * @Route("/test", name="test")
     */

    public function test(Request $req)
    {
        if($req->isXmlHttpRequest()){
            $idGame = $req->get("idGame");
            $id_player = $req->get("id_player");

            $players = $this->playerRepo->findBy("game_id=".$idGame);

            return new JsonResponse(array("data" => json_encode($players)));
        }
    }   
}
