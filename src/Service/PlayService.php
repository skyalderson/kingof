<?php

namespace App\Service;

use App\Entity\Player;
use App\Repository\PlayerRepository;

class PlayService
{
    private $playerRepo;

    public function __construct(PlayerRepository $playerRepo)
    {
        $this->playerRepo = $playerRepo;
    }

    public function getPlayer($idPlayer)
    {
        $player = $this->playerRepo->find($idPlayer);
        return $player;
    }

    public function addGP(Player $player, $nbGP)
    {
        $newGP = $player->getGp() + $nbGP;
        return $newGP;
    }

    public function isCheating($idPlayer, $idLoggedUser)
    {
        $player = $this->playerRepo->find($idPlayer);

        if (null === $player) {
            return 'ko';
        } else {
            $idActiveUser = $player->getUser()->getId();
            $return = ($idActiveUser == $idLoggedUser);

            return $return;
        }
    }

    public function isPlaying($idPlayer)
    {
        $player = $this->playerRepo->find($idPlayer);

        if (null === $player) {
            return 'ko';
        } else {
            $return = ($player->getIsPlaying()) ? 'ok' : 'ko';

            return $return;
        }
    }

    public function isInCity($idPlayer)
    {
        $player = $this->playerRepo->find($idPlayer);

        if (null === $player) {
            return 'ko';
        } else {
            $return = (0 === $player->getInCity());

            return !$return;
        }
    }
}
