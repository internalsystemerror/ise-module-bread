<?php

namespace Ise\Bread\Options;

use Ise\Bread\Exception\InvalidArgumentException;
use ReflectionClass;
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
     * Set alias
     *
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = (string) $alias;
        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set class
     *
     * @param string $class
     * @return self
     */
    public function setClass($class)
    {
        $this->class = (string) $class;
        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Check that class implements interface
     *
     * @param string $class
     * @param string $interface
     * @throws InvalidArgumentException
     */
    protected function classImplementsInterface($class, $interface)
    {
        // Check class exists
        $classString = (string) $class;
        if (!class_exists($classString)) {
            throw new InvalidArgumentException(sprintf(
                'Class "%s" does not exist.',
                $classString
            ));
        }

        // Check class implements interface
        $reflection      = new ReflectionClass($classString);
        $interfaceString = (string) $interface;
        if (!$reflection->implementsInterface($interfaceString)) {
            throw new InvalidArgumentException(sprintf(
                'Class "%s" does not implement the interface "%s".',
                $classString,
                $interfaceString
            ));
        }
    }
}
