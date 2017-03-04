<?php

namespace Ise\Bread\Controller\Factory;

use Interop\Container\ContainerInterface;
use Ise\Bread\EventManager\BreadEventManager;
use Ise\Bread\ServiceManager\BreadManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BreadActionControllerFactory implements FactoryInterface
{

    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $breadManager    = $container->get(BreadManager::class);
        $controllerClass = $breadManager->getControllerBaseClass($requestedName);
        
        return new $controllerClass(
            $container->get(BreadEventManager::class),
            $breadManager->getService($breadManager->getServiceClassFromControllerClass($requestedName)),
            $breadManager->getControllerOptionsFromControllerClass($requestedName)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this($serviceLocator->getServiceLocator(), $requestedName);
    }
}
