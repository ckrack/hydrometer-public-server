<?php
namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks()
 */
abstract class Entity implements JsonSerializable
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var Integer
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @var \DateTime
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @var \DateTime
     */
    protected $updated;

    /**
     * we call parent::__construct so we need this here..
     */
    public function __construct()
    {
    }

    /**
     * setter for arrays
     * @param array $options [description]
     */
    public function import(array $options)
    {
        $_classMethods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $_classMethods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * [getCreated description]
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * [getUpdated description]
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * [jsonSerialize description]
     * @return [type] [description]
     */
    public function jsonSerialize()
    {
        $entity = [];
        $methods = get_class_methods(get_class($this));
        foreach ($methods as $method) {
            if (preg_match('/get([A-Z][a-z]+)/', $method, $match)) {
                $prop = strtolower($match[1]);
                if (isset($this->{$prop})) {
                    $entity[$prop] = $this->{$method}();
                }

            }
        }
        return $entity;
    }

    /**
     * @return Integer
     */
    public function getId()
    {
        return $this->id;
    }
}
