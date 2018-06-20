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
 */
abstract class AbstractBasicEntity extends AbstractEntity implements BasicEntityInterface
{

    /**
     * @ORM\Column(type="string", length=128, nullable=false)
     * @ZF\Flags({"priority": 11})
     * @ZF\Options({"label": "Name"})
     * @ZF\Filter({"name": "StripNewlines"})
     * @ZF\Validator({"name": "StringLength", "options": {"min": 3}})
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @ZF\Flags({"priority": 10})
     * @ZF\Options({"label": "Description"})
     * @var string
     */
    protected $description;

    /**
     * Cast to string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string|null $description
     *
     * @return void
     */
    public function setDescription(string $description = null): void
    {
        $this->description = $description;
    }
}
