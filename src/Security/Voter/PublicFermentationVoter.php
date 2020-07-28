<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Security\Voter;

use App\Entity\Fermentation;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * This voter grants access to public fermentations.
 */
final class PublicFermentationVoter extends Voter
{
    // these strings are just invented: you can use anything
    public const VIEW = 'view';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (self::VIEW !== $attribute) {
            return false;
        }
        // only vote on fermentations
        return $subject instanceof Fermentation;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // fermentations is public?
        return $subject->isPublic();

        throw new \LogicException('This code should not be reached!');
    }
}
