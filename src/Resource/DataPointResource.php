<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Resource;

use App\Entity\Fermentation;
use App\Entity\Hydrometer;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Exception;

/**
 * Class Resource.
 */
class DataPointResource extends EntityRepository
{
    /**
     * Get the latest values from a hydrometer.
     *
     * @param Hydrometer $hydrometer [description]
     *
     * @return [type] [description]
     */
    public function findInColumns(Hydrometer $hydrometer = null)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->select("DATE_FORMAT(d.created, '%Y-%m-%d %H:%i') time,
                         AVG(d.temperature) temperature,
                         AVG(d.angle) angle,
                         AVG(d.gravity) gravity,
                         AVG(d.trubidity) trubidity,
                         AVG(d.battery) battery,
                         ROUND(UNIX_TIMESTAMP(d.created) / 1800) groupTime,
                         h.name hydrometer,
                         h.metricTemperature,
                         h.metricGravity")
                ->from('App\Entity\DataPoint', 'd')
                ->leftJoin('App\Entity\Hydrometer', 'h', 'WITH', 'd.hydrometer = h')
                ->orderBy('d.created', 'ASC')
                ->groupBy('groupTime');

            if ($hydrometer) {
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
     * @param Fermentation $fermentation [description]
     *
     * @return [type] [description]
     */
    public function findByFermentation(Fermentation $fermentation)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->select("DATE_FORMAT(d.created, '%Y-%m-%d %H:%i') time,
                         AVG(d.temperature) temperature,
                         AVG(d.angle) angle,
                         AVG(d.gravity) gravity,
                         AVG(d.trubidity) trubidity,
                         AVG(d.battery) battery,
                         ROUND(UNIX_TIMESTAMP(d.created) / 1800) groupTime")
                ->from('App\Entity\DataPoint', 'd')
                ->orderBy('d.created', 'ASC')
                ->groupBy('groupTime')
                ->andWhere('d.fermentation = :fermentation')
                ->setParameter('fermentation', $fermentation->getId());

            $qb->setMaxResults(500);

            $q = $qb->getQuery();

            return $q->getArrayResult();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get the latest values from a hydrometer.
     *
     * @param User $user [description]
     *
     * @return [type] [description]
     */
    public function findAllByUser(User $user, Hydrometer $hydrometer = null, $limit = 500, $offset = 0)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->select("d.id, DATE_FORMAT(d.created, '%Y-%m-%d %H:%i') time, d.temperature, d.angle, d.gravity, d.trubidity, d.battery,
                h.name hydrometer,
                h.metricTemperature,
                h.metricGravity")
                ->from('App\Entity\DataPoint', 'd')
                ->join('App\Entity\Hydrometer', 'h', 'WITH', 'd.hydrometer = h')
                ->orderBy('d.created', 'DESC')
                ->groupBy('time')
                ->andWhere('h.user = :user')
                ->setParameter('user', $user->getId())
                ->setFirstResult($offset)
                ->setMaxResults($limit);

            if ($hydrometer) {
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
     *
     * @param Fermentation $fermentation [description]
     * @param Hydrometer   $hydrometer   [description]
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
               ->andWhere('d.created >= :begin')
               ->setParameter('begin', $fermentation->getBegin())
               ->set('d.fermentation', ':fermentation')
               ->setParameter('fermentation', $fermentation);

            // use end if supplied
            if (null !== $fermentation->getEnd()) {
                $qb->andWhere('d.created < :end')
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
     * @param Fermentation  $fermentation [description]
     * @param DateTime|null $before       [description]
     * @param DateTime|null $after        [description]
     *
     * @return [type] [description]
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
                $orX->add($qb->expr()->lt('d.created', ':before'));
                $qb->setParameter('before', $before);
            }

            if ($after instanceof \DateTime) {
                $orX->add($qb->expr()->gte('d.created', ':after'));
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
                    d.battery,
                    d.trubidity')
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
