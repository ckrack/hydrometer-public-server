<?php

namespace App\Repository;

use App\Entity\Hydrometer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hydrometer>
 *
 * @method Hydrometer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hydrometer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hydrometer[]    findAll()
 * @method Hydrometer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HydrometerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hydrometer::class);
    }
}
