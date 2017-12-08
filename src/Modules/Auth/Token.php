<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Modules\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

/**
 * Authenticate by a token.
 */
class Token
{
    /**
     * PSR-3 logger.
     *
     * @var [type]
     */
    protected $logger;

    /**
     * Doctrine Entitymanager.
     *
     * @var [type]
     */
    protected $em;

    /**
     * @param EntityManager   $em     [description]
     * @param LoggerInterface $logger [description]
     */
    public function __construct(
        EntityManager $em,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function authenticate($token)
    {
        try {
            $qb = $this->em->createQueryBuilder();

            $q = $qb->select('h.id hydrometer_id, f.id fermentation_id, u.id user_id')
                ->from('App\Entity\Token', 't')
                ->join('App\Entity\Hydrometer', 'h', 'WITH', 'h.token = t.id')
                ->leftJoin('App\Entity\Fermentation', 'f', 'WITH', 'f.hydrometer = h.id AND (f.end IS NULL OR f.end > NOW())')
                ->leftJoin('App\Entity\User', 'u', 'WITH', 'h.user = u.id')
                ->setMaxResults(1)
                ->andWhere('t.value = :token')
                ->setParameter('token', $token)
                ->getQuery();

            return $q->getSingleResult();
        } catch (\Exception $e) {
            $this->logger->error($e);
            throw new \InvalidArgumentException('Authentication failed');
        }
    }

    public function identify($token)
    {
        $authData = $this->authenticate($token);
        $this->logger->debug('authData', $authData);

        return $this->em->find(User::class, $authData['user_id']);
    }
}
