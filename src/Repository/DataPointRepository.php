<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Repository;

use App\Entity\DataPoint;
use App\Entity\Fermentation;
use App\Entity\Hydrometer;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;

/**
 * Class Repository.
 */
final class DataPointRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(DataPoint::class));
    }

    public function save(DataPoint $dataPoint)
    {
        $this->getEntityManager()->persist($dataPoint);
        $this->getEntityManager()->flush();
    }

    public function delete(DataPoint $dataPoint)
    {
        $this->getEntityManager()->remove($dataPoint);
        $this->getEntityManager()->flush();
    }

    /**
     * Get the latest values from a hydrometer.
     */
    public function findInColumns(Hydrometer $hydrometer = null):? array
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->select("DATE_FORMAT(d.createdAt, '%Y-%m-%d %H:%i') time,
                         AVG(d.temperature) temperature,
                         AVG(d.angle) angle,
                         AVG(d.gravity) gravity,
                         AVG(d.battery) battery,
                         ROUND(UNIX_TIMESTAMP(d.createdAt) / 1800) groupTime,
                         h.name hydrometer,
                         h.metricTemperature,
                         h.metricGravity")
                ->from('App\Entity\DataPoint', 'd')
                ->leftJoin('App\Entity\Hydrometer', 'h', 'WITH', 'd.hydrometer = h')
                ->orderBy('d.createdAt', 'ASC')
                ->groupBy('groupTime');

            if ($hydrometer !== null) {
                $qb->andWhere('d.hydrometer = :hydrometer');
                $qb->setParameter('hydrometer', $hydrometer->getId());
            }

            $qb->setMaxResults(500);

            $q = $qb->getQuery();

            return $q->getArrayResult();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get the latest values from a fermentation.
     *
     */
    public function findByFermentation(Fermentation $fermentation):? array
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->select("DATE_FORMAT(d.createdAt, '%Y-%m-%d %H:%i') time,
                         AVG(d.temperature) temperature,
                         AVG(d.angle) angle,
                         AVG(d.gravity) gravity,
                         AVG(d.battery) battery,
                         ROUND(UNIX_TIMESTAMP(d.createdAt) / 1800) groupTime")
                ->from('App\Entity\DataPoint', 'd')
                ->orderBy('d.createdAt', 'ASC')
                ->groupBy('groupTime')
                ->andWhere('d.fermentation = :fermentation')
                ->setParameter('fermentation', $fermentation->getId());

            $qb->setMaxResults(3000);

            $q = $qb->getQuery();

            return $q->getArrayResult();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get the latest values from a hydrometer.
     */
    public function findAllByUser(User $user, Hydrometer $hydrometer = null, $limit = 500, $offset = 0):? array
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->select("d.id, DATE_FORMAT(d.createdAt, '%Y-%m-%d %H:%i') time, d.temperature, d.angle, d.gravity, d.battery,
                h.name hydrometer,
                h.metricTemperature,
                h.metricGravity")
                ->from('App\Entity\DataPoint', 'd')
                ->join('App\Entity\Hydrometer', 'h', 'WITH', 'd.hydrometer = h')
                ->orderBy('d.createdAt', 'DESC')
                ->groupBy('time')
                ->andWhere('h.user = :user')
                ->setParameter('user', $user->getId())
                ->setFirstResult($offset)
                ->setMaxResults($limit);

            if ($hydrometer !== null) {
                $qb->andWhere('d.hydrometer = :hydrometer');
                $qb->setParameter('hydrometer', $hydrometer->getId());
            }

            $q = $qb->getQuery();

            return $q->getArrayResult();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Add all un-assigned datapoints that match a fermentations timerange and
     * the defined hydrometer to a (new) fermentation.
     */
    public function addToFermentation(Fermentation $fermentation, Hydrometer $hydrometer)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->update('App\Entity\DataPoint', 'd')
                ->andWhere('d.fermentation IS NULL')
                ->andWhere('d.hydrometer = :hydrometer')
                ->setParameter('hydrometer', $hydrometer)
                ->andWhere('d.createdAt >= :begin')
                ->setParameter('begin', $fermentation->getBegin())
                ->set('d.fermentation', ':fermentation')
                ->setParameter('fermentation', $fermentation);

            // use end if supplied
            if (null !== $fermentation->getEnd()) {
                $qb->andWhere('d.createdAt < :end')
                    ->setParameter('end', $fermentation->getEnd());
            }

            $q = $qb->getQuery();

            return $q->execute();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Remove datapoints from fermentation.
     * If the before and after parameters are supplied, only datapoints.
     *
     */
    public function removeFromFermentation(Fermentation $fermentation, DateTime $before = null, DateTime $after = null)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->update('App\Entity\DataPoint', 'd')
                ->andWhere('d.fermentation = :fermentation')
                ->set('d.fermentation', 'NULL')
                ->setParameter('fermentation', $fermentation);

            $orX = $qb->expr()->orX();

            if ($before instanceof \DateTime) {
                $orX->add($qb->expr()->lt('d.createdAt', ':before'));
                $qb->setParameter('before', $before);
            }

            if ($after instanceof \DateTime) {
                $orX->add($qb->expr()->gte('d.createdAt', ':after'));
                $qb->setParameter('after', $after);
            }

            $qb->andWhere($orX);

            $q = $qb->getQuery();

            return $q->execute();
        } catch (Exception $e) {
            return null;
        }
    }

    public function findActivity($activity_id)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $q = $qb->select('
                    d.temperature,
                    d.angle,
                    d.gravity,
                    d.battery')
                ->from('App\Entity\DataPoint', 'd')
                ->andWhere('d = :activity')
                ->setParameter('activity', $activity_id)
                ->getQuery();

            return $q->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        } catch (Exception $e) {
            return null;
        }
    }
}
