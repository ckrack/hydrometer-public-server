<?php
namespace App\Resource;

use Doctrine\ORM\EntityRepository;
use App\Entity\Hydrometer;
use App\Entity\User;
use App\Entity\Fermentation;

/**
 * Class Resource
 */
class DataPointResource extends EntityRepository
{
    /**
     * Get the latest values from a hydrometer
     * @param  Hydrometer $hydrometer [description]
     * @return [type]           [description]
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
                         s.name hydrometer")
                ->from('App\Entity\DataPoint', 'd')
                ->leftJoin('App\Entity\Hydrometer', 's', 'WITH', 'd.hydrometer = s')
                ->orderBy('d.created', 'ASC')
                ->groupBy('groupTime');


            if ($hydrometer) {
                $qb->andWhere('d.hydrometer = :hydrometer');
                $qb->setParameter('hydrometer', $hydrometer->getId());
            }

            $qb->setMaxResults(500);

            $q = $qb->getQuery();
            return $q->getArrayResult();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the latest values from a fermentation
     * @param  Fermentation $fermentation [description]
     * @return [type]           [description]
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
        } catch (\Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    /**
     * Get the latest values from a hydrometer
     * @param  User $user [description]
     * @return [type]           [description]
     */
    public function findAllByUser(User $user, Hydrometer $hydrometer = null, $limit = 500, $offset = 0)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->select("d.id, DATE_FORMAT(d.created, '%Y-%m-%d %H:%i') time, d.temperature, d.angle, d.gravity, d.trubidity, d.battery, s.name hydrometer")
                ->from('App\Entity\DataPoint', 'd')
                ->join('App\Entity\Hydrometer', 's', 'WITH', 'd.hydrometer = s')
                ->orderBy('d.created', 'DESC')
                ->groupBy('time')
                ->andWhere('s.user = :user')
                ->setParameter('user', $user->getId())
                ->setFirstResult($offset)
                ->setMaxResults($limit);

            if ($hydrometer) {
                $qb->andWhere('d.hydrometer = :hydrometer');
                $qb->setParameter('hydrometer', $hydrometer->getId());
            }

            $q = $qb->getQuery();
            return $q->getArrayResult();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Add all un-assigned datapoints that match a fermentations timerange and
     * the defined hydrometer to a (new) fermentation.
     * @param Fermentation $fermentation [description]
     * @param Hydrometer      $hydrometer      [description]
     */
    public function addToFermentation(Fermentation $fermentation, Hydrometer $hydrometer)
    {
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

        $q = $qb->getQuery();

        return $q->execute();
    }
}
