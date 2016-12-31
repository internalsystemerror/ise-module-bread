<?php

namespace Ise\Bread\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation as ZF;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractBasicEntity extends AbstractEntity
{

    /**
     * @ORM\Column(type="string", length=128, nullable=false)
     * @ZF\Flags({"priority": 100})
     * @ZF\Options({"label": "Name"})
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @ZF\Flags({"priority": 100})
     * @ZF\Options({"label": "Description"})
     * @var string
     */
    protected $description;

    /**
     * Cast to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Permission
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Permission
     */
    public function setDescription($description)
    {
        $this->description = (string) $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
