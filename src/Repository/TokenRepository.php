<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Repository;

use App\Entity\Token;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;

/**
 * Class Repository.
 */
final class TokenRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Token::class));
    }

    public function save(Token $token)
    {
        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush();
    }

    /**
     * Get the latest values from a hydrometer.
     *
     * @return [type] [description]
     */
    public function findByValue($token)
    {
        try {
            return $this->findOneBy(['value' => $token]);
        } catch (Exception $exception) {
            return null;
        }
    }
}
