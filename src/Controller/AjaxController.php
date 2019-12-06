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
     * @Route("/test", name="test")
     */

    public function test(GameRepository $gameRepo)
    {
        $idGame = 20;
    
        $game = $gameRepo->find($idGame);
        $_repoPlayers = $game->getPlayers()->toArray();

        foreach($_repoPlayers as $p)
        {
            $_players[$p->getId()] = array();
            $_players[$p->getId()]['id'] = $p->getId();
            $_players[$p->getId()]['monster'] = ($p->getMonster() == null) ? 0 : $p->getMonster()->getId();
            $_players[$p->getId()]['ready'] = $p->getIsReady();

        }
        return $this->render('test/index.html.twig', [
            'players' => $_players,
        ]);
    }

    /**
     * @Route("/lobby/data", name="lobby.data", methods={"POST"})
    */
    public function updateData(Request $req, GameRepository $gameRepo)
    {
        if($req->isXmlHttpRequest()){
            
            $idGame = $req->get("idGame");
            $idPlayer = $req->get("idPlayer");

            $isKicked = true;
            $isWaiting = true;
            $_players = array();

            $game = $gameRepo->find($idGame);
            if($game === null){
                $isGameExisting = false;
            }
            else{
                $isGameExisting = true;

                $isWaiting = ($game->getState() == 1) ? true : false;
                if($isWaiting){
                    $_repoPlayers = $game->getPlayers()->toArray();
                    foreach($_repoPlayers as $p)
                    {
                        if($idPlayer != $p->getId())
                        {
                            $_players[$p->getId()] = array();
                            $_players[$p->getId()]['id'] = $p->getId();
                            $_players[$p->getId()]['monster'] = ($p->getMonster() == null) ? "Aucun monstre sélectionné" : $p->getMonster()->getName();
                            $_players[$p->getId()]['ready'] = ($p->getIsReady()) ? 1 : 0;
                            $_players[$p->getId()]['name'] = $p->getUser()->getUsername();
                        }
                        else{
                            $isKicked = false;
                        }
                    }
                }
   
            }
            return new JsonResponse([
                "exists" => json_encode($isGameExisting), 
                "waiting" => json_encode($isWaiting),
                "kicked" => json_encode($isKicked), 
                "data" => json_encode($_players)]);
        }
        else return new Response("ERREUR", 400);
    }

    /**
     * @Route("/lobby/ready", name="lobby.ready", methods={"POST"})
    */
    public function readyState(Request $req, PlayerRepository $playerRepo)
    {
        if($req->isXmlHttpRequest()){
            
            $idPlayer = $req->get("idPlayer");
            $readyTxt = $req->get("ready");

            $ready = ($readyTxt == "true") ? true : false ;

            $player = $playerRepo->find($idPlayer);
            $player->setIsReady($ready);

            $em = $this->getDoctrine()->getManager();
            $em->persist($player);
            $em->flush();

            return new Response("YOOOOOOOOOOO");
        }
        else return new Response("ERREUR", 400);
    }

    
    /**
     * @Route("/lobby/select/monster", name="lobby.select.monster", methods={"POST"})
    */

    public function selectMonster(Request $req, PlayerRepository $playerRepo, MonsterRepository $monsterRepo)
    {
        if($req->isXmlHttpRequest()){
            
            // TODO : METTRE A JOUR LES SELECT/TOGGLE
            
            $idGame = $req->get("idGame");
            $idPlayer = $req->get("idPlayer");
            $idMonster = $req->get("idMonster");

            $_players = $playerRepo->findMonstersWithoutMe($idGame, $idPlayer);

            $isTaken = false;
            foreach($_players as $p)
            {
                if($p->getMonster() !== null && $p->getMonster()->getId() == $idMonster )  $isTaken = true; 
            }
            $em = $this->getDoctrine()->getManager();
            $player = $playerRepo->find($idPlayer);

            if(!$isTaken)
            {
                $monster = $monsterRepo->find($idMonster);
                $player->setMonster($monster);

                
                $em->persist($player);
                $em->flush();
                return new Response($idMonster);
            }
            else
            {
                $player->setMonster(null);
                $em->persist($player);
                $em->flush();
                return new Response("taken");
            }
            //return new JsonResponse(array("data" => json_encode($players)));
        }
        else return new Response("ERREUR", 400);
    }   
}
