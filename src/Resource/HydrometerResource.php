<?php
namespace App\Resource;

use Doctrine\ORM\EntityRepository;
use App\Entity\Hydrometer;
use App\Entity\User;

/**
 * Class Resource
 */
class HydrometerResource extends EntityRepository
{
    /**
     * Get the latest values from a hydrometer
     * @param  Hydrometer $hydrometer [description]
     * @return [type]           [description]
     */
    public function getData(Hydrometer $hydrometer, $hours = null, $since = null)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->select('UNIX_TIMESTAMP(d.created) unixtime, d.temperature, d.angle, d.gravity, d.trubidity')
                ->from('App\Entity\DataPoint', 'd')
                ->join('d.hydrometer', 's')
                ->orderBy('d.created', 'ASC')
                ->andWhere('s = :hydrometer')
                ->setParameter('hydrometer', $hydrometer);

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
     * Get Datetime of last reset time for a hydrometer.
     * @param  [type] $hydrometer [description]
     * @return [type]          [description]
     */
    public function getLastResetTime($hydrometer)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $q = $qb->select('MAX(d.created) time')
                ->from('App\Entity\DataPoint', 'd')
                ->join('d.hydrometer', 's')
                ->andWhere('s = :hydrometer')
                ->setParameter('hydrometer', $hydrometer)
                ->getQuery();

            return $q->getSingleScalarResult();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get the latest values from a hydrometer
     * @param  Hydrometer $hydrometer [description]
     * @return [type]           [description]
     */
    public function getLatestData(Hydrometer $hydrometer)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $q = $qb->select('UNIX_TIMESTAMP(d.created) time, d.temperature, d.angle, d.battery, d.gravity, d.trubidity, s.name')
                ->from('App\Entity\DataPoint', 'd')
                ->join('d.hydrometer', 's')
                ->orderBy('d.created', 'DESC')
                ->setMaxResults(1)
                ->andWhere('s = :hydrometer')
                ->setParameter('hydrometer', $hydrometer)
                ->getQuery();

            return $q->getSingleResult();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get list of hydrometers including their last activity
     * @param  Hydrometer $hydrometer [description]
     * @return [type]           [description]
     */
    public function findAllWithLastActivity(User $user)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $q = $qb->select('MAX(d.created) activity, d.temperature, d.angle, d.battery, d.gravity, d.trubidity, s.name, s.id')
                ->from('App\Entity\Hydrometer', 's')
                ->leftJoin('App\Entity\DataPoint', 'd', 'WITH', 'd.hydrometer = s')
                ->orderBy('activity', 'DESC')
                ->andWhere('s.user = :user')
                ->setParameter('user', $user->getId())
                ->groupBy('s')
                ->getQuery();

            return $q->getArrayResult();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get list of hydrometers including their last activity
     * @param  Hydrometer $hydrometer [description]
     * @return [type]           [description]
     */
    public function findAllByUser(User $user)
    {
        try {
            return $this->findBy(['user' => $user]);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function formByUser(User $user)
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
     * @param  User $user [description]
     * @return [type]           [description]
     */
    public function getLastActive(User $user = null)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $q = $qb->select('s')
                ->from('App\Entity\Hydrometer', 's')
                ->join('App\Entity\DataPoint', 'd', 'WITH', 'd.hydrometer = s')
                ->orderBy('d.created', 'DESC')
                ->setMaxResults(1)
                ->getQuery();

            // limit to user
            if ($user instanceof App\Entity\User) {
                $q->andWhere('s.user = :user')
                  ->setParameter('user', $user->getId());
            }

            return $q->getSingleResult();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function findOneByUser($hydrometer, $user)
    {
        try {
            return $this->findOneBy([
                'user' => $user,
                'id' => $hydrometer
            ]);
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
            // try to find an existing spindel
            $hydrometer = $this->find($id);

            if ($hydrometer instanceof \App\Entity\Hydrometer) {
                return $hydrometer;
            }

            // we create an un-identified hydrometer
            $hydrometer = new Hydrometer;
            $hydrometer->setId($id);

            return $hydrometer;
        } catch (\Exception $e) {
            throw new \Exception('Can not create hydrometer');
        }
    }
}
