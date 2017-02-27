<?php

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
     * @return self
     */
    public function setClass($class)
    {
        parent::setClass($class);
        if (class_exists($this->class)) {
            $this->setBaseClass($this->class);
        }
        return $this;
    }
    
    /**
     * Set base class
     * 
     * @param string $class
     * @return self
     */
    public function setBaseClass($class)
    {
        $this->baseClass = (string) $class;
        return $this;
    }

    /**
     * Get base class
     * 
     * @return string
     */
    public function getBaseClass()
    {
        return $this->baseClass;
    }

    /**
     * Set factory
     * 
     * @param string $factory
     * @return self
     */
    public function setFactory($factory)
    {
        $this->factory = (string) $factory;
        return $this;
    }

    /**
     * Get factory
     * 
     * @return string
     */
    public function getFactory()
    {
        return $this->factory;
    }
}
