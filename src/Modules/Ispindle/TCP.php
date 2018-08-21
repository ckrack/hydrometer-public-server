<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Modules\Ispindle;

use App\Entity\DataPoint;
use App\Entity\Fermentation;
use App\Entity\Hydrometer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class TCP
{
    protected $logger;
    protected $em;

    /**
     * Use League\Container for auto-wiring dependencies into the controller.
     *
     * @param LoggerInterface $logger [description]
     */
    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function authenticate($token)
    {
        try {
            $qb = $this->em->createQueryBuilder();

            $q = $qb->select('h.id hydrometer_id, f.id fermentation_id')
                ->from('App\Entity\Token', 't')
                ->join('App\Entity\Hydrometer', 'h', 'WITH', 'h.token = t.id')
                ->leftJoin('App\Entity\Fermentation', 'f', 'WITH', 'f.hydrometer = h.id AND (f.end IS NULL OR f.end > NOW())')
                ->setMaxResults(1)
                ->andWhere('t.value = :token')
                ->setParameter('token', $token)
                ->getQuery();

            return $q->getSingleResult();
        } catch (\Exception $e) {
            $this->logger->error($e);
            throw new \InvalidArgumentException('Authentication failed');
        }
    }

    public function validateInput($input)
    {
        $input = trim($input);

        // first sign {
        if (0 === !mb_strpos($input, '{')) {
            $this->logger->error('First sign not {');

            return false;
        }

        // last sign }
        if (!mb_strpos($input, '}') === mb_strlen($input)) {
            $this->logger->error('Last sign not }');

            return false;
        }

        return true;
    }

    public function saveData($data, $hydrometer, $fermentation)
    {
        $hydrometer = $this->em->getRepository('App\Entity\Hydrometer')->find($hydrometer);

        // set the hydrometer name if specified
        if (isset($data['name'])) {
            $hydrometer->setName($data['name']);
        }

        // set the hydrometer id if specified
        if (isset($data['ID'])) {
            $hydrometer->setEspId($data['ID']);
        }

        $this->logger->debug('Spindel: Receive data for Hydrometer', [$hydrometer, $data, $fermentation]);

        $dataPoint = new DataPoint();

        unset($data['id'], $data['ID']);
        $dataPoint->import($data);

        if ($fermentation) {
            $fermentation = $this->em->getRepository('App\Entity\Fermentation')->find($fermentation);
            $dataPoint->setFermentation($fermentation);
        }

        $dataPoint->setHydrometer($hydrometer);

        $this->em->persist($hydrometer);
        $this->em->persist($dataPoint);

        $this->em->flush();
    }
}
