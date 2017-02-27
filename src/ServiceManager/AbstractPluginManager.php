<?php

namespace Ise\Bread\ServiceManager;

use Zend\ServiceManager\AbstractPluginManager as ZendAbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;

abstract class AbstractPluginManager extends ZendAbstractPluginManager
{
    
    /**
     * @var string
     */
    protected $instanceOf;
    
    /**
     * {@inheritDoc}
     */
    public function validatePlugin($instance)
    {
        if ($instance instanceof $this->instanceOf) {
            return;
        }

        throw new InvalidServiceException(sprintf(
            '%s expects an instance of %s, %s given.',
            __CLASS__,
            $this->instanceOf,
            is_object($instance) ? get_class($instance) : gettype($instance)
        ));
    }
}
