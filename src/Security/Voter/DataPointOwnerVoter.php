<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Security\Voter;

use App\Entity\DataPoint;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * This voter decides whether a user is the owner of a subject and grants access if so.
 * The supported attributes are view and edit.
 */
class DataPointOwnerVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT], true)) {
            return false;
        }

        if (!$subject instanceof DataPoint) {
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

        // user and the user of the hydrometer, the datapoint is added from are equal?
        return $user === $subject->getHydrometer()->getUser();

        throw new \LogicException('This code should not be reached!');
    }
}
