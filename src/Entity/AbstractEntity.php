<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation as ZF;

/**
 * @ORM\MappedSuperclass
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
abstract class AbstractEntity implements EntityInterface
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
     * {@inheritDoc}
     */
    public function __construct()
    {
        $this->lastModified = new DateTime;
        $this->created      = new DateTime;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * {@inheritDoc}
     */
    public function setDisabled($disabled)
    {
        $this->disabled = (boolean)$disabled;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * {@inheritDoc}
     */
    public function setLastModified(DateTime $lastModifed)
    {
        $this->lastModified = $lastModifed;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * {@inheritDoc}
     */
    public function setCreated(DateTime $created)
    {
        $this->created = $created;
        return $this;
    }
}
