<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HydrometerRepository")
 * @ORM\Table(
 *     name="hydrometers",
 *     options={
 *         "collate"="utf8mb4_unicode_ci",
 *         "charset"="utf8mb4"
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(
 *             name="id",
 *             columns={"id"}
 *         )
 *     }
 * )
 */
class Hydrometer extends Entity implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * This is the ESP8266 Arduino ChipId.
     * http://esp8266.github.io/Arduino/versions/2.0.0/doc/libraries.html#esp-specific-apis.
     *
     * @ORM\Column(name="esp_id", type="string", nullable=true)
     *
     * @var string
     */
    protected $esp_id;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     *
     * @var string
     */
    protected $name;

    /**
     * The metric of the temperature units (Celsius / Fahrenheit).
     *
     * @ORM\Column(type="string", length=190, nullable=true)
     *
     * @var string
     */
    protected $metricTemperature;

    /**
     * The metric of the gravity units (SG, Plato, Brix).
     *
     * @ORM\Column(type="string", length=190, nullable=true)
     *
     * @var string
     */
    protected $metricGravity;

    /**
     * Update interval in ms.
     *
     * @ORM\Column(name="`interval`", type="integer", nullable=true)
     *
     * @var int
     */
    protected $interval;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="hydrometers")
     * ORM\JoinColumn(
     *     name="user_id",
     *     referencedColumnName="id",
     *     nullable=true
     * )
     */
    protected $user;

    /**
     * @ORM\OneToOne(targetEntity="Token")
     * ORM\JoinColumn(
     *     name="token_id",
     *     referencedColumnName="id",
     *     nullable=true,
     *     unique=true
     * )
     */
    protected $token;

    /**
     * @ORM\OneToMany(targetEntity="Fermentation", mappedBy="user")
     */
    protected $fermentations;

    /**
     * Setter for Id.
     * This is the only Id we allow to be set manually, as we use the one from the ESP board.
     *
     * @param int $id the id of the ESP-Board
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param Token $token
     *
     * @return self
     */
    public function setToken($token)
    {
        $this->token = $token;

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

    public function getFermentations()
    {
        return $this->fermentations;
    }

    /**
     * @return int
     */
    public function getEspId()
    {
        return $this->esp_id;
    }

    /**
     * @param int $esp_id
     *
     * @return self
     */
    public function setEspId($esp_id)
    {
        $this->esp_id = $esp_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetricTemperature()
    {
        return $this->metricTemperature;
    }

    /**
     * @param string $metricTemperature
     *
     * @return self
     */
    public function setMetricTemperature($metricTemperature)
    {
        $this->metricTemperature = $metricTemperature;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetricGravity()
    {
        return $this->metricGravity;
    }

    /**
     * @param string $metricGravity
     *
     * @return self
     */
    public function setMetricGravity($metricGravity)
    {
        $this->metricGravity = $metricGravity;

        return $this;
    }

    /**
     * Get update interval in ms.
     *
     * @return int
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Set update interval in ms.
     *
     * @param int $interval update interval in ms
     *
     * @return self
     */
    public function setInterval(?int $interval)
    {
        $this->interval = $interval;

        return $this;
    }
}
