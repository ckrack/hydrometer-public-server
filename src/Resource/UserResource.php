<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Resource;

use App\Entity\Hydrometer;
use Doctrine\ORM\EntityRepository;

/**
 * Class Resource.
 */
class UserResource extends EntityRepository
{
    /**
     * Get the latest values from a hydrometer.
     *
     * @param Hydrometer $hydrometer [description]
     *
     * @return [type] [description]
     */
    public function findByEmail($email)
    {
        try {
            return $this->findOneBy(['email' => $email]);
        } catch (\Exception $e) {
            return null;
        }
    }
}
