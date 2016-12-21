<?php

namespace IseBread\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractActionControllerFactory implements FactoryInterface
{
    
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $serviceClass = $this->translateControllerToService($requestedName);
        $service      = $container->getServiceLocator()->get($serviceClass);
        
        return new $requestedName($service);
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this($serviceLocator, $requestedName);
    }

    /**
     * Translate controller name to service class
     *
     * @param  string $controllerName Controller name to translate to service class
     * @return boolean
     */
    protected function translateControllerToService($controllerName)
    {
        $serviceClass = trim(str_replace('Controller', 'Service', $controllerName), '\\');
        if (!$serviceClass) {
            return false;
        }
        
        return class_exists($serviceClass) ? $serviceClass : false;
    }
}
