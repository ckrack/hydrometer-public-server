<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Repository;

use App\Entity\Hydrometer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;

/**
 * Class Repository.
 */
final class HydrometerRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Hydrometer::class));
    }

    public function save(Hydrometer $hydrometer)
    {
        $this->getEntityManager()->persist($hydrometer);
        $this->getEntityManager()->flush();
    }

    public function delete(Hydrometer $hydrometer)
    {
        $this->getEntityManager()->remove($hydrometer);
        $this->getEntityManager()->flush();
    }

    /**
     * Get the latest values from a hydrometer.
     */
    public function getData(Hydrometer $hydrometer, $hours = null, $since = null): ? array
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->select('
                UNIX_TIMESTAMP(d.createdAt) unixtime,
                d.temperature,
                d.angle,
                d.gravity')
                ->from(\App\Entity\DataPoint::class, 'd')
                ->join('d.hydrometer', 'h')
                ->orderBy('d.createdAt', 'ASC')
                ->andWhere('h = :hydrometer')
                ->setParameter('hydrometer', $hydrometer);

            if ($hours) {
                $qb->andWhere('d.createdAt >= DATE_SUB(NOW(), '.$hours.", 'HOUR')");
                $qb->andWhere('d.createdAt <= NOW()');
            }

            if ($since) {
                $qb->andWhere('d.createdAt >= :since');
                $qb->setParameter('since', $since);
            }

            $qb->setMaxResults(2000);

            $q = $qb->getQuery();

            return $q->getResult();
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Get the latest values from a hydrometer.
     */
    public function getLatestData(Hydrometer $hydrometer): ? array
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $q = $qb->select('
                UNIX_TIMESTAMP(d.createdAt) time,
                d.temperature,
                d.angle,
                d.battery,
                d.gravity,
                h.name')
                ->from(\App\Entity\DataPoint::class, 'd')
                ->join('d.hydrometer', 'h')
                ->orderBy('d.createdAt', 'DESC')
                ->setMaxResults(1)
                ->andWhere('h = :hydrometer')
                ->setParameter('hydrometer', $hydrometer)
                ->getQuery();

            return $q->getSingleResult();
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Get list of hydrometers including their last activity.
     */
    public function findAllWithLastActivity(User $user): ? array
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $q = $qb->select('
                    MAX(d.createdAt) activity,
                    MAX(d.id) last_datapoint_id,
                    MAX(d.gravity) max_gravity,
                    h.name,
                    h.id,
                    h.metricTemperature,
                    h.metricGravity')
                ->from(\App\Entity\Hydrometer::class, 'h')
                ->leftJoin(\App\Entity\DataPoint::class, 'd', 'WITH', 'd.hydrometer = h AND d.deletedAt IS NULL')
                ->orderBy('activity', 'DESC')
                ->andWhere('h.user = :user')
                ->setParameter('user', $user->getId())
                ->groupBy('h')
                ->getQuery();

            return $q->getArrayResult();
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Get list of hydrometers including their last activity.
     */
    public function findAllByUser(User $user): ? array
    {
        try {
            return $this->findBy(['user' => $user]);
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Get hydrometers for form.
     */
    public function formByUser(User $user): array
    {
        $hydrometers = $this->findAllByUser($user);
        $options = [];
        foreach ($hydrometers as $hydrometer) {
            $options[$hydrometer->getId()] = $hydrometer->getName();
        }

        return $options;
    }

    /**
     * Get the latest active hydrometer, optionally by given user.
     */
    public function getLastActive(User $user = null): ? Hydrometer
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->select('s')
                ->from(\App\Entity\Hydrometer::class, 's')
                ->join(\App\Entity\DataPoint::class, 'd', 'WITH', 'd.hydrometer = s')
                ->orderBy('d.createdAt', 'DESC')
                ->setMaxResults(1);

            // limit to user
            if ($user instanceof User) {
                $qb->andWhere('s.user = :user')
                    ->setParameter('user', $user->getId());
            }

            $q = $qb->getQuery();

            return $q->getSingleResult();
        } catch (Exception $exception) {
            return null;
        }
    }

    public function findOneByUser($hydrometer, $user): ? Hydrometer
    {
        try {
            return $this->findOneBy([
                'user' => $user,
                'id' => $hydrometer,
            ]);
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @return array
     */
    public function getOrCreate($id): Hydrometer
    {
        try {
            // try to find an existing spindel
            $hydrometer = $this->find($id);

            if ($hydrometer instanceof Hydrometer) {
                return $hydrometer;
            }

            // we create an un-identified hydrometer
            $hydrometer = new Hydrometer();
            $hydrometer->setId($id);

            return $hydrometer;
        } catch (Exception $exception) {
            throw new Exception('Can not create hydrometer', $exception->getCode(), $exception);
        }
    }
}
