<?php

namespace App\Security;

use App\Entity\Player;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PlayerVoter extends Voter
{
    // these strings are just invented: you can use anything
    const KICK = 'kick';
    const QUIT = 'quit';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::KICK, self::QUIT])) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Player) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Player object, thanks to supports
        /** @var Player $player */
        $player = $subject;

        switch ($attribute) {
            case self::KICK:
                return $this->canKick($player, $user);
            case self::QUIT:
                return $this->canQuit($player, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canKick(Player $player, User $user)
    {
        $game = $player->getGame();
        if ($game->getCreatorUser() == $user) {
            return true;
        }
    }

    private function canQuit(Player $player, User $user)
    {
        if ($player->getUser() == $user) {
            return true;
        }
    }
}
