<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Security\Voter;

use App\Entity\Calibration;
use App\Entity\Fermentation;
use App\Entity\Hydrometer;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * This voter decides whether a user is the owner of a subject and grants access if so.
 * The supported attributes are view and edit.
 */
final class OwnerVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!\in_array($attribute, [self::VIEW, self::EDIT], true)) {
            return false;
        }

        // only vote on some subjects
        $subjects = [
            Fermentation::class,
            Calibration::class,
            Hydrometer::class,
        ];

        return \in_array(\get_class($subject), $subjects, true);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // user and subject-user (owner) are equal
        return $user === $subject->getUser();

        throw new \LogicException('This code should not be reached!');
    }
}
