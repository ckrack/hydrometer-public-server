<?php
namespace App\Entity;

use App\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Resource\HydrometerResource")
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
class Hydrometer extends Entity\Entity
{
    public function __construct()
    {
        parent::__construct();
        $this->items = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * This is the ESP8266 Arduino ChipId.
     * http://esp8266.github.io/Arduino/versions/2.0.0/doc/libraries.html#esp-specific-apis
     * @ORM\Column(name="esp_id", type="string", nullable=true)
     * @var string
     */
    protected $esp_id;

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     * @var string
     */
    protected $name;

    /**
     * The metric of the temperature units (Celsius / Fahrenheit)
     * @ORM\Column(type="string", length=190, nullable=true)
     * @var string
     */
    protected $metricTemperature;

    /**
     * The metric of the gravity units (SG, Plato, Brix)
     * @ORM\Column(type="string", length=190, nullable=true)
     * @var string
     */
    protected $metricGravity;

    /**
     * @ORM\ManyToOne(targetEntity="User")
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
     * @ORM\Column(name="changed", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"name", "token", "user", "esp_id"})
     * @var \DateTime
     */
    protected $contentChanged;

    /**
     * Setter for Id.
     * This is the only Id we allow to be set manually, as we use the one from the ESP board.
     * @param integer $id the id of the ESP-Board.
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

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFermentations()
    {
        return $this->fermentations;
    }

    /**
     * @return \DateTime $contentChanged
     */
    public function getContentChanged()
    {
        return $this->contentChanged;
    }

    /**
     * @return Integer
     */
    public function getEspId()
    {
        return $this->esp_id;
    }

    /**
     * @param Integer $esp_id
     *
     * @return self
     */
    public function setEspId(int $esp_id)
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
}
