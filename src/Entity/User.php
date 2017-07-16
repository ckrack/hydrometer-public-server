<?php
namespace App\Entity;

use App\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Resource\UserResource")
 * @Gedmo\SoftDeleteable(fieldName="deleted", timeAware=true)
 * @ORM\Table(name="users", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 */
class User extends Entity\Entity
{
    public function __construct()
    {
        parent::__construct();
        $this->fermentations = new ArrayCollection();
        $this->spindles = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="string", length=190)
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=190)
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @var string
     */
    protected $apiToken;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $timeZone;

    /**
     * @ORM\OneToMany(targetEntity="Spindle", mappedBy="user")
     */
    protected $spindles;

    /**
     * @ORM\OneToMany(targetEntity="Fermentation", mappedBy="user")
     */
    protected $fermentations;

    /**
     * @ORM\Column(name="changed", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"username", "password"})
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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = md5($password);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSpindles()
    {
        return $this->spindles;
    }

    /**
     * @return mixed
     */
    public function getFermentations()
    {
        return $this->fermentations;
    }

    /**
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param string $apiToken
     *
     * @return self
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;

        return $this;
    }
}
