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
 * @ORM\Entity(repositoryClass="App\Resource\TokenResource")
 * @ORM\Table(name="token", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 */
class Token extends Entity
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * One of api, login, register.
     *
     * @ORM\Column(type="string", length=190, nullable=true)
     *
     * @var string
     */
    protected $type;

    /**
     * @ORM\Column(name="value", type="string", length=190, nullable=true)
     *
     * @var string
     */
    protected $value;

    /**
     * @ORM\Column(name="was_used", type="integer", length=1, nullable=true)
     *
     * @var string
     */
    protected $wasUsed;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="token")
     * ORM\JoinColumn(
     *     name="user_id",
     *     referencedColumnName="id",
     *     nullable=true
     * )
     */
    protected $user;

    /**
     * @ORM\Column(name="changed", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"value", "type", "user", "wasUsed"})
     *
     * @var \DateTime
     */
    protected $contentChanged;

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

    /**
     * @return \DateTime $contentChanged
     */
    public function getContentChanged()
    {
        return $this->contentChanged;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return self
     */
    public function setContentChanged(\DateTime $contentChanged)
    {
        $this->contentChanged = $contentChanged;

        return $this;
    }

    /**
     * @return string
     */
    public function getWasUsed()
    {
        return $this->wasUsed;
    }

    /**
     * @param string $wasUsed
     *
     * @return self
     */
    public function setWasUsed($wasUsed)
    {
        $this->wasUsed = $wasUsed;

        return $this;
    }
}
