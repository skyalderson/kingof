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

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::KICK])) {
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
                return $this->canDelete($player, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canDelete(Player $player, User $user)
    {       
        $game = $player->getGame();
        if ($game->getCreatorUser() == $user) return true;
    }

}

?>