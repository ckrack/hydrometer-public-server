<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Repository;

use App\Entity\Hydrometer;
use Doctrine\ORM\EntityRepository;
use Exception;

/**
 * Class Repository.
 */
class TokenRepository extends EntityRepository
{
    /**
     * Get the latest values from a hydrometer.
     *
     * @return [type] [description]
     */
    public function findByValue($token)
    {
        try {
            return $this->findOneBy(['value' => $token]);
        } catch (Exception $e) {
            return null;
        }
    }
}
