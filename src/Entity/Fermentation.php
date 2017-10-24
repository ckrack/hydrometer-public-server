<?php
namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Resource\FermentationResource")
 * @Gedmo\SoftDeleteable(fieldName="deleted", timeAware=true)
 * @ORM\Table(name="fermentations", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 */
class Fermentation extends Entity
{
    public function __construct()
    {
        parent::__construct();
        $this->data = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
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
     * @ORM\ManyToOne(targetEntity="User")
     * ORM\JoinColumn(
     *     name="user_id",
     *     referencedColumnName="id"
     * )
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Calibration")
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
     * @var \DateTime
     */
    protected $begin;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $end;

    /**
     * @ORM\Column(name="changed", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"name", "calibration", "user"})
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
    public function getCalibration()
    {
        return $this->calibration;
    }

    /**
     * @param mixed $calibration
     *
     * @return self
     */
    public function setCalibration($calibration)
    {
        $this->calibration = $calibration;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     *
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
}
