<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Resource\DataPointResource")
 * @Gedmo\SoftDeleteable(fieldName="deleted", timeAware=true)
 * @ORM\Table(name="data_points", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 */
class DataPoint extends Entity
{
    /**
     * @ORM\ManyToOne(targetEntity="Hydrometer")
     * ORM\JoinColumn(
     *     name="hydrometer_id",
     *     referencedColumnName="id"
     * )
     */
    protected $hydrometer;

    /**
     * @ORM\ManyToOne(targetEntity="Fermentation")
     * ORM\JoinColumn(
     *     name="fermentation_id",
     *     referencedColumnName="id"
     * )
     */
    protected $fermentation;

    /**
     * @ORM\Column(name="changed", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"angle", "temperature", "battery", "gravity", "trubidity"})
     * @var \DateTime
     */
    protected $contentChanged;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deleted;

    /**
     * [getContentChanged description]
     * @return \DateTime
     */
    public function getContentChanged()
    {
        return $this->contentChanged;
    }

    /**
     * @ORM\Column(type="float", nullable=true)
     * @var string
     */
    protected $angle;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @var string
     */
    protected $temperature;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @var string
     */
    protected $battery;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @var string
     */
    protected $gravity;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @var string
     */
    protected $trubidity;

    /**
     * @return mixed
     */
    public function getHydrometer()
    {
        return $this->hydrometer;
    }

    /**
     * @param mixed $hydrometer
     *
     * @return self
     */
    public function setHydrometer($hydrometer)
    {
        $this->hydrometer = $hydrometer;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFermentation()
    {
        return $this->fermentation;
    }

    /**
     * @param mixed $fermentation
     *
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
}
