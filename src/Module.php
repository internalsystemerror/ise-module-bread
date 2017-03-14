<?php

namespace Ise\Bread;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface ;

class Module implements
    ConfigProviderInterface,
    DependencyIndicatorInterface,
    InitProviderInterface
{

    /**
     * {@inheritDoc}
     */
    public function init(ModuleManagerInterface  $moduleManager)
    {
        $eventManager = $moduleManager->getEventManager();
        
        $configListener = new Listener\ConfigListener();
        $configListener->attach($eventManager);
    }


    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include realpath(__DIR__ . '/../config/module.config.php');
    }

    /**
     * {@inheritDoc}
     */
    public function getModuleDependencies()
    {
        return [
            'DoctrineModule',
            'DoctrineORMModule',
        ];
    }
}
