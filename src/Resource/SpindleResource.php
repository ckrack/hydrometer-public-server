<?php
namespace App\Resource;

use Doctrine\ORM\EntityRepository;
use App\Entity\Spindle;
use App\Entity\User;

/**
 * Class Resource
 */
class SpindleResource extends EntityRepository
{
    /**
     * Get the latest values from a spindle
     * @param  Spindle $spindle [description]
     * @return [type]           [description]
     */
    public function getData(Spindle $spindle, $hours = null, $since = null)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->select('UNIX_TIMESTAMP(d.created) unixtime, d.temperature, d.angle, d.gravity, d.trubidity')
                ->from('App\Entity\DataPoint', 'd')
                ->join('d.spindle', 's')
                ->orderBy('d.created', 'ASC')
                ->andWhere('s = :spindle')
                ->setParameter('spindle', $spindle);

            if ($hours) {
                $qb->andWhere("d.created >= DATE_SUB(NOW(), ".$hours.", 'HOUR')");
                $qb->andWhere('d.created <= NOW()');
            }

            if ($since) {
                $qb->andWhere("d.created >= :since");
                $qb->setParameter('since', $since);
            }

            $qb->setMaxResults(2000);

            $q = $qb->getQuery();

            return $q->getResult();
        } catch (\Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    /**
     * Get Datetime of last reset time for a spindle.
     * @param  [type] $spindle [description]
     * @return [type]          [description]
     */
    public function getLastResetTime($spindle)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $q = $qb->select('MAX(d.created) time')
                ->from('App\Entity\DataPoint', 'd')
                ->join('d.spindle', 's')
                ->andWhere('s = :spindle')
                ->setParameter('spindle', $spindle)
                ->getQuery();

            return $q->getSingleScalarResult();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get the latest values from a spindle
     * @param  Spindle $spindle [description]
     * @return [type]           [description]
     */
    public function getLatestData(Spindle $spindle)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $q = $qb->select('UNIX_TIMESTAMP(d.created) time, d.temperature, d.angle, d.battery, d.gravity, d.trubidity, s.name')
                ->from('App\Entity\DataPoint', 'd')
                ->join('d.spindle', 's')
                ->orderBy('d.created', 'DESC')
                ->setMaxResults(1)
                ->andWhere('s = :spindle')
                ->setParameter('spindle', $spindle)
                ->getQuery();

            return $q->getSingleResult();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get list of spindles including their last activity
     * @param  Spindle $spindle [description]
     * @return [type]           [description]
     */
    public function findAllWithLastActivity()
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $q = $qb->select('MAX(d.created) activity, d.temperature, d.angle, d.battery, d.gravity, d.trubidity, s.name, s.id')
                ->from('App\Entity\Spindle', 's')
                ->leftJoin('App\Entity\DataPoint', 'd', 'WITH', 'd.spindle = s')
                ->orderBy('activity', 'DESC')
                ->groupBy('s')
                ->getQuery();

            return $q->getArrayResult();
        } catch (\Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    /**
     * Get the latest active spindle, optionally by given user.
     * @param  User $user [description]
     * @return [type]           [description]
     */
    public function getLastActive(User $user = null)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $q = $qb->select('s')
                ->from('App\Entity\Spindle', 's')
                ->join('App\Entity\DataPoint', 'd', 'WITH', 'd.spindle = s')
                ->orderBy('d.created', 'DESC')
                ->setMaxResults(1)
                ->getQuery();

            // limit to user
            if ($user instanceof App\Entity\User) {
                $q->andWhere('s.user = :user')
                  ->setParameter('user', $user);
            }

            return $q->getSingleResult();
        } catch (\Exception $e) {
            return null;
        }
    }


    /**
     * @param int|null $iso
     *
     * @return array
     */
    public function getOrCreate($id)
    {
        try {
            $spindle = $this->find($id);
            if ($spindle instanceof \App\Entity\Spindle) {
                return $spindle;
            }

            // we create an un-identified spindle
            $spindle = new Spindle;
            $spindle->setId($id);

            return $spindle;
        } catch (\Exception $e) {
            throw new \Exception('Can not create spindle');
        }
    }
}
