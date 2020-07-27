<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="calibrations", options={"collate": "utf8mb4_unicode_ci", "charset": "utf8mb4"})
 */
class Calibration extends Entity implements TimestampableInterface, SoftDeletableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Hydrometer")
     * ORM\JoinColumn(
     *     name="hydrometer_id",
     *     referencedColumnName="id"
     * )
     */
    protected $hydrometer;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="float")
     *
     * @var float
     */
    protected $const1;

    /**
     * @ORM\Column(type="float")
     *
     * @var float
     */
    protected $const2;

    /**
     * @ORM\Column(type="float")
     *
     * @var float
     */
    protected $const3;

    /**
     * @ORM\OneToMany(targetEntity="Fermentation", mappedBy="calibration")
     */
    protected $fermentations;

    public function getHydrometer()
    {
        return $this->hydrometer;
    }

    /**
     * @return self
     */
    public function setHydrometer($hydrometer)
    {
        $this->hydrometer = $hydrometer;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getConst1()
    {
        return $this->const1;
    }

    /**
     * @param string $const1
     *
     * @return self
     */
    public function setConst1($const1)
    {
        $this->const1 = $const1;

        return $this;
    }

    /**
     * @return string
     */
    public function getConst2()
    {
        return $this->const2;
    }

    /**
     * @param string $const2
     *
     * @return self
     */
    public function setConst2($const2)
    {
        $this->const2 = $const2;

        return $this;
    }

    /**
     * @return string
     */
    public function getConst3()
    {
        return $this->const3;
    }

    /**
     * @param string $const3
     *
     * @return self
     */
    public function setConst3($const3)
    {
        $this->const3 = $const3;

        return $this;
    }

    public function getFermentations()
    {
        return $this->fermentations;
    }

    public function __construct()
    {
        parent::__construct();
        $this->fermentations = new \Doctrine\Common\Collections\ArrayCollection();
    }
}
