<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Options;

use Ise\Bread\Exception\InvalidArgumentException;
use Zend\Stdlib\AbstractOptions;

abstract class AbstractClassOptions extends AbstractOptions
{

    /**
     * @var string
     */
    protected $alias = '';

    /**
     * @var string
     */
    protected $class = '';

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * Set alias
     *
     * @param string $alias
     *
     * @return void
     */
    public function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Set class
     *
     * @param string $class
     *
     * @return void
     */
    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    /**
     * Check that class implements interface
     *
     * @param string $class
     * @param string $interface
     *
     * @throws InvalidArgumentException
     * @throws \ReflectionException
     */
    protected function classImplementsInterface(string $class, string $interface): void
    {
        // Check class exists
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf(
                'Class "%s" does not exist.',
                $class
            ));
        }

        // Check class implements interface
        $reflection = new \ReflectionClass($class);
        if (!$reflection->implementsInterface($interface)) {
            throw new InvalidArgumentException(sprintf(
                'Class "%s" does not implement the interface "%s".',
                $class,
                $interface
            ));
        }
    }
}
