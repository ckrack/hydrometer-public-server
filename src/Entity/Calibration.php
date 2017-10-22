<?php
namespace App\Entity;

use App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deleted", timeAware=true)
 * @ORM\Table(name="calibrations", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 */
class Calibration extends Entity\Entity
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
     * @ORM\Column(type="string", length=190, nullable=true)
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="float")
     * @var string
     */
    protected $const1;

    /**
     * @ORM\Column(type="float")
     * @var string
     */
    protected $const2;

    /**
     * @ORM\Column(type="float")
     * @var string
     */
    protected $const3;

    /**
     * @ORM\OneToMany(targetEntity="Fermentation", mappedBy="calibration")
     */
    protected $fermentations;

    /**
     * @ORM\Column(name="changed", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"name", "const1", "const2", "const3"})
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

    /**
     * @return mixed
     */
    public function getFermentations()
    {
        return $this->fermentations;
    }
}
