<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Entity;

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
     * @ORM\Column(type="bool", nullable=false)
     * @ZF\Exclude()
     * @var bool
     */
    protected $disabled = false;

    /**
     * @ORM\Column(type="datetime", nullable=false, name="last_modified")
     * @ZF\Exclude()
     * @var \DateTime
     */
    protected $lastModified;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @ZF\Exclude()
     * @var \DateTime
     */
    protected $created;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $this->lastModified = new \DateTime;
        $this->created      = new \DateTime;
    }

    /**
     * @inheritdoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @inheritdoc
     */
    public function setDisabled(bool $disabled): void
    {
        $this->disabled = $disabled;
    }

    /**
     * @inheritdoc
     */
    public function getLastModified(): \DateTime
    {
        return $this->lastModified;
    }

    /**
     * @inheritdoc
     */
    public function setLastModified(\DateTime $lastModifed): void
    {
        $this->lastModified = $lastModifed;
    }

    /**
     * @inheritdoc
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    /**
     * @inheritdoc
     */
    public function setCreated(\DateTime $created): void
    {
        $this->created = $created;
    }
}
