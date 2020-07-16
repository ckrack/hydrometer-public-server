<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FermentationRepository")
 * @ORM\Table(name="fermentations", options={"collate": "utf8mb4_unicode_ci", "charset": "utf8mb4"})
 */
class Fermentation extends Entity implements TimestampableInterface, SoftDeletableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    public function __construct()
    {
        parent::__construct();
        $this->data = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Hydrometer")
     * ORM\JoinColumn(
     *     name="hydrometer_id",
     *     referencedColumnName="id"
     * )
     */
    protected $hydrometer;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="fermentations")
     * ORM\JoinColumn(
     *     name="user_id",
     *     referencedColumnName="id"
     * )
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Calibration", inversedBy="fermentations")
     * ORM\JoinColumn(
     *     name="calibration_id",
     *     referencedColumnName="id",
     *     nullable=true
     * )
     */
    protected $calibration;

    /**
     * @ORM\OneToMany(targetEntity="DataPoint", mappedBy="fermentation")
     */
    protected $data;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    protected $begin;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    protected $end;

    /**
     * @ORM\Column(name="is_public", type="boolean", nullable=true)
     *
     * @var bool
     */
    protected $public;

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

    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getCalibration()
    {
        return $this->calibration;
    }

    /**
     * @return self
     */
    public function setCalibration($calibration)
    {
        $this->calibration = $calibration;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @return self
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return \DateTime $begin
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * @param \DateTime $begin
     *
     * @return self
     */
    public function setBegin($begin)
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * @return \DateTime $end
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     *
     * @return self
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * @param bool $public
     *
     * @return self
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }
}
