<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Request\ParamConverter;

use Doctrine\ORM\EntityManagerInterface;
use Jenssegers\Optimus\Optimus;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

final class OptimusConverter implements ParamConverterInterface
{
    protected EntityManagerInterface $em;
    protected Optimus $optimus;
    protected LoggerInterface $logger;

    /**
     * Use auto-wiring.
     *
     * @param EntityManagerInterface $em      [description]
     * @param Optimus                $optimus [description]
     */
    public function __construct(
        EntityManagerInterface $em,
        Optimus $optimus,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->optimus = $optimus;
        $this->logger = $logger;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        try {
            $name = $configuration->getName();
            $class = $configuration->getClass();
            if ($request->attributes->has($name)) {
                $rawValue = $request->attributes->get($name);
                $value = $this->optimus->decode($rawValue);
                $object = $this->em->find($class, $value);

                $request->attributes->set($name, $object);
            }
        } catch (\Exception $e) {
            return;
        }
    }

    public function supports(ParamConverter $configuration)
    {
        // if there is no manager, this means that only Doctrine DBAL is configured
        if (null === $this->em) {
            return false;
        }
        return null !== $configuration->getClass();
    }
}
