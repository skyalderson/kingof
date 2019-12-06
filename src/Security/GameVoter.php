<?php

namespace App\Security;

use App\Entity\Game;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class GameVoter extends Voter
{
    // these strings are just invented: you can use anything
    const DELETE = 'delete';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::DELETE])) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Game) {
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

        // you know $subject is a Game object, thanks to supports
        /** @var Game $game */
        $game = $subject;

        switch ($attribute) {
            case self::DELETE:
                return $this->canDelete($game, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canDelete(Game $game, User $user)
    {       
        if ($game->getCreatorUser() == $user) return true;
    }

}

?>