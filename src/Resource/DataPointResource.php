<?php
namespace App\Resource;

use Doctrine\ORM\EntityRepository;
use App\Entity\Spindle;
use App\Entity\User;

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

            $qb->select("DATE_FORMAT(d.created, '%Y-%m-%d %H:%i') time, AVG(d.temperature) temperature, AVG(d.angle) angle, d.gravity, d.trubidity")
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
            echo $e->getMessage();
            return null;
        }
    }
}
