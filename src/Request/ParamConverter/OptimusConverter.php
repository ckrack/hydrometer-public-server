<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Request\ParamConverter;

use Doctrine\ORM\EntityManagerInterface;
use Jenssegers\Optimus\Optimus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

final class OptimusConverter implements ParamConverterInterface
{
    private EntityManagerInterface $em;
    private Optimus $optimus;

    public function __construct(
        EntityManagerInterface $em,
        Optimus $optimus
    ) {
        $this->em = $em;
        $this->optimus = $optimus;
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
