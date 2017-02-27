<?php

namespace Ise\Bread\Factory;

use Interop\Container\ContainerInterface;
use Ise\Bread\ServiceManager\BreadManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BreadServiceFactory implements FactoryInterface
{
    
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $breadManager = $container->get(BreadManager::class);
        $serviceClass = $breadManager->getServiceBaseClass($requestedName);
        
        return new $serviceClass(
            $breadManager,
            $breadManager->getMapper($breadManager->getMapperClassFromServiceClass($requestedName)),
            $breadManager->getFormsFromServiceClass($requestedName)
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
