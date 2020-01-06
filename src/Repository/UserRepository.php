<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Repository;

use App\Entity\Hydrometer;
use Doctrine\ORM\EntityRepository;

/**
 * Class Repository.
 */
class UserRepository extends EntityRepository
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
