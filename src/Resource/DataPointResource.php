<?php
namespace App\Resource;

use Doctrine\ORM\EntityRepository;
use App\Entity\Spindle;
use App\Entity\User;
use App\Entity\Fermentation;

/**
 * Class Resource
 */
class DataPointResource extends EntityRepository
{
    /**
     * Get the latest values from a spindle
     * @param  Spindle $spindle [description]
     * @return [type]           [description]
     */
    public function findInColumns(Spindle $spindle = null)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->select("DATE_FORMAT(d.created, '%Y-%m-%d %H:%i') time, AVG(d.temperature) temperature, AVG(d.angle) angle, AVG(d.gravity) gravity, AVG(d.trubidity) trubidity, AVG(d.battery) battery")
                ->from('App\Entity\DataPoint', 'd')
                ->orderBy('d.created', 'ASC')
                ->groupBy('time');


            if ($spindle) {
                $qb->andWhere('d.spindle = :spindle');
                $qb->setParameter('spindle', $spindle->getId());
            }

            $qb->setMaxResults(500);

            $q = $qb->getQuery();
            return $q->getArrayResult();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the latest values from a spindle
     * @param  User $user [description]
     * @return [type]           [description]
     */
    public function findAllByUser(User $user, Spindle $spindle = null, $limit = 500, $offset = 0)
    {
        try {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->select("d.id, DATE_FORMAT(d.created, '%Y-%m-%d %H:%i') time, d.temperature, d.angle, d.gravity, d.trubidity, d.battery")
                ->from('App\Entity\DataPoint', 'd')
                ->join('App\Entity\Spindle', 's')
                ->orderBy('d.created', 'DESC')
                ->groupBy('time')
                ->andWhere('s.user = :user')
                ->setParameter('user', $user->getId())
                ->setFirstResult($offset)
                ->setMaxResults($limit);

            if ($spindle) {
                $qb->andWhere('d.spindle = :spindle');
                $qb->setParameter('spindle', $spindle->getId());
            }

            $q = $qb->getQuery();
            return $q->getArrayResult();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function addToFermentation(Fermentation $fermentation, Spindle $spindle)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->update('App\Entity\DataPoint', 'd')
           ->andWhere('d.fermentation IS NULL')
           ->andWhere('d.spindle = :spindle')
           ->setParameter('spindle', $spindle)
           ->andWhere('d.created >= :begin')
           ->setParameter('begin', $fermentation->getBegin())
           ->set('d.fermentation',':fermentation')
           ->setParameter('fermentation', $fermentation);

        $q = $qb->getQuery();

        return $q->execute();
    }
}
