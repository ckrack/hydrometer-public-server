<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(
 *     name="users",
 *     options={
 *         "collate"="utf8mb4_unicode_ci",
 *         "charset"="utf8mb4"
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(
 *             name="email",
 *             columns={"email"}
 *         )
 *     }
 * )
 */
class User extends Entity implements UserInterface, \Serializable, EquatableInterface, TimestampableInterface, SoftDeletableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    public function __construct()
    {
        parent::__construct();
        $this->fermentations = new ArrayCollection();
        $this->hydrometers = new ArrayCollection();
        $this->token = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="string", length=190, nullable=true)
     *
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="string", name="facebook_id", nullable=true)
     */
    protected $facebookId;

    /**
     * @ORM\Column(type="string", name="google_id", nullable=true)
     */
    protected $googleId;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $timeZone;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $language;

    /**
     * @ORM\OneToMany(targetEntity="Hydrometer", mappedBy="user")
     */
    protected $hydrometers;

    /**
     * @ORM\OneToMany(targetEntity="Fermentation", mappedBy="user")
     */
    protected $fermentations;

    /**
     * @ORM\OneToMany(targetEntity="Token", mappedBy="user")
     */
    protected $token;

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * @param string $username
     *
     * @return self
     */
    public function setUsername($username)
    {
        $this->setEmail($username);

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

    public function getPassword()
    {
        // no password is used
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        return $this;
    }

    public function getHydrometers()
    {
        return $this->hydrometers;
    }

    public function getFermentations()
    {
        return $this->fermentations;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     *
     * @return self
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @return self
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * @return self
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * @param string $timeZone
     *
     * @return self
     */
    public function setTimeZone($timeZone)
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials()
    {
        // no password is used
    }

    public function getSalt()
    {
        // no password is used
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize([
            $this->id,
        ]);
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        [$this->id] = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function isEqualTo(UserInterface $user)
    {
        if ($this->id !== $user->getId()) {
            return false;
        }

        return true;
    }
}
