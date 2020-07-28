<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Repository;

use App\Entity\Fermentation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;

/**
 * Class Repository.
 */
final class FermentationRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Fermentation::class));
    }

    public function save(Fermentation $fermentation)
    {
        $this->getEntityManager()->persist($fermentation);
        $this->getEntityManager()->flush();
    }

    public function delete(Fermentation $fermentation)
    {
        $this->getEntityManager()->remove($fermentation);
        $this->getEntityManager()->flush();
    }

    /**
     * Get the latest values from a hydrometer.
     *
     * @param User $user [description]
     *
     * @return [type] [description]
     */
    public function findAllByUser(User $user, $limit = 500, $offset = 0)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->select("
                f.id, f.name,
                DATE_FORMAT(f.begin, '%Y-%m-%d %H:%i') begin,
                DATE_FORMAT(f.end, '%m-%d %H:%i') AS ending,
                DATE_FORMAT(MAX(d.createdAt), '%Y-%m-%d %H:%i') AS activity,
                AVG(d.temperature) AS temperature,
                MAX(d.temperature) AS max_temperature,
                MIN(d.temperature) AS min_temperature,
                MAX(d.angle) AS max_angle,
                MIN(d.angle) AS min_angle,
                MAX(d.gravity) AS max_gravity,
                MIN(d.gravity) AS min_gravity,
                h.name hydrometer,
                h.metricTemperature,
                h.metricGravity
            ")
                ->from(\App\Entity\Fermentation::class, 'f')
                ->leftJoin(\App\Entity\DataPoint::class, 'd', 'WITH', 'd.fermentation = f')
                ->join(\App\Entity\Hydrometer::class, 'h', 'WITH', 'f.hydrometer = h')
                ->orderBy('begin', 'DESC')
                ->groupBy('f')
                ->andWhere('f.user = :user')
                ->setParameter('user', $user->getId())
                ->setFirstResult($offset)
                ->setMaxResults($limit);

            $q = $qb->getQuery();

            return $q->getArrayResult();
        } catch (Exception $exception) {
            return null;
        }
    }

    public function findOneByUser($fermentation, User $user)
    {
        try {
            return $this->findOneBy([
                'user' => $user,
                'id' => $fermentation,
            ]);
        } catch (Exception $exception) {
            return null;
        }
    }
}
