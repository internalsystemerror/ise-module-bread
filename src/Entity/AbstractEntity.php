<?php

namespace Ise\Bread\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation as ZF;

/**
 * @ORM\MappedSuperclass
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
abstract class AbstractEntity
{

    /**
     * @var string
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @ZF\Exclude()
     * @var boolean
     */
    protected $disabled = false;

    /**
     * @ORM\Column(type="datetime", nullable=false, name="last_modified")
     * @ZF\Exclude()
     * @var DateTime
     */
    protected $lastModified;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @ZF\Exclude()
     * @var DateTime
     */
    protected $created;

    /**
     * Constructor method
     */
    public function __construct()
    {
        $this->lastModified = new DateTime;
        $this->created      = new DateTime;
    }
    
    /**
     * Cast object to a string
     *
     * @return string
     */
    abstract public function __toString();

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set disabled
     *
     * @param boolean $disabled
     * @return self
     */
    public function setDisabled($disabled)
    {
        $this->disabled = (boolean) $disabled;
        return $this;
    }

    /**
     * Is disabled
     *
     * @return boolean
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * Set last modified
     *
     * @param DateTime $lastModifed Last modified date
     * @return self
     */
    public function setLastModified(DateTime $lastModifed)
    {
        $this->lastModified = $lastModifed;
        return $this;
    }

    /**
     * Get last modified
     *
     * @return DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Set created
     *
     * @param DateTime $created Created date
     * @return self
     */
    public function setCreated(DateTime $created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }
}
