<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Options;

abstract class AbstractFactoryClassOptions extends AbstractClassOptions
{

    /**
     * @var string
     */
    protected $baseClass = '';

    /**
     * @var string
     */
    protected $factory = '';

    /**
     * Set class
     *
     * @param string $class
     *
     * @return void
     */
    public function setClass(string $class): void
    {
        parent::setClass($class);
        if (class_exists($class)) {
            $this->setBaseClass($class);
        }
    }

    /**
     * Get base class
     *
     * @return string
     */
    public function getBaseClass(): string
    {
        return $this->baseClass;
    }

    /**
     * Set base class
     *
     * @param string $class
     *
     * @return void
     */
    public function setBaseClass(string $class): void
    {
        $this->baseClass = $class;
    }

    /**
     * Get factory
     *
     * @return string
     */
    public function getFactory(): string
    {
        return $this->factory;
    }

    /**
     * Set factory
     *
     * @param string $factory
     *
     * @return void
     */
    public function setFactory(string $factory): void
    {
        $this->factory = $factory;
    }
}
