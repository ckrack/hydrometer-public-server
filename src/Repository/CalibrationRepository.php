<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Repository;

use App\Entity\Calibration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Class Repository.
 */
final class CalibrationRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Calibration::class));
    }

    public function save(Calibration $calibration)
    {
        $this->getEntityManager()->persist($calibration);
        $this->getEntityManager()->flush();
    }

    public function delete(Calibration $calibration)
    {
        $this->getEntityManager()->remove($calibration);
        $this->getEntityManager()->flush();
    }
}
