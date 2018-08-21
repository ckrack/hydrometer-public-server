<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Resource\DataPointResource")
 * @Gedmo\SoftDeleteable(fieldName="deleted", timeAware=true)
 * @ORM\Table(name="data_points", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 */
class DataPoint extends Entity
{
    /**
     * @ORM\ManyToOne(targetEntity="Hydrometer", fetch="EAGER")
     * ORM\JoinColumn(
     *     name="hydrometer_id",
     *     referencedColumnName="id"
     * )
     */
    protected $hydrometer;

    /**
     * @ORM\ManyToOne(targetEntity="Fermentation", inversedBy="data")
     * ORM\JoinColumn(
     *     name="fermentation_id",
     *     referencedColumnName="id"
     * )
     */
    protected $fermentation;

    /**
     * @ORM\Column(name="changed", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"angle", "temperature", "battery", "gravity", "trubidity"})
     *
     * @var \DateTime
     */
    protected $contentChanged;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deleted;

    /**
     * [getContentChanged description].
     *
     * @return \DateTime
     */
    public function getContentChanged()
    {
        return $this->contentChanged;
    }

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @var float
     */
    protected $angle;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @var float
     */
    protected $temperature;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @var float
     */
    protected $battery;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @var float
     */
    protected $gravity;

    /**
     * @ORM\Column(type="float", nullable=true)
     * Trubidity for a trubidity sensor
     *
     * @var float
     */
    protected $trubidity;

    /**
     * @ORM\Column(type="float", nullable=true)
     * Wifi strength
     *
     * @var float
     */
    protected $RSSI;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * Update interval
     *
     * @var int
     */
    protected $interval;

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

    public function getFermentation()
    {
        return $this->fermentation;
    }

    /**
     * @return self
     */
    public function setFermentation($fermentation)
    {
        $this->fermentation = $fermentation;

        return $this;
    }

    /**
     * @return string
     */
    public function getAngle()
    {
        return $this->angle;
    }

    /**
     * @param string $angle
     *
     * @return self
     */
    public function setAngle($angle)
    {
        $this->angle = $angle;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemperature()
    {
        return $this->temperature;
    }

    /**
     * @param string $temperature
     *
     * @return self
     */
    public function setTemperature($temperature)
    {
        $this->temperature = $temperature;

        return $this;
    }

    /**
     * @return string
     */
    public function getBattery()
    {
        return $this->battery;
    }

    /**
     * @param string $battery
     *
     * @return self
     */
    public function setBattery($battery)
    {
        $this->battery = $battery;

        return $this;
    }

    /**
     * @return string
     */
    public function getGravity()
    {
        return $this->gravity;
    }

    /**
     * @param string $gravity
     *
     * @return self
     */
    public function setGravity($gravity)
    {
        $this->gravity = $gravity;

        return $this;
    }

    /**
     * @return string
     */
    public function getTrubidity()
    {
        return $this->trubidity;
    }

    /**
     * @param string $trubidity
     *
     * @return self
     */
    public function setTrubidity($trubidity)
    {
        $this->trubidity = $trubidity;

        return $this;
    }

    /**
     * @return float
     */
    public function getRSSI()
    {
        return $this->RSSI;
    }

    /**
     * @param float $RSSI
     *
     * @return self
     */
    public function setRSSI($RSSI)
    {
        $this->RSSI = $RSSI;

        return $this;
    }

    /**
     * @return int
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @param int $interval
     *
     * @return self
     */
    public function setInterval($interval)
    {
        $this->interval = $interval;

        return $this;
    }
}
