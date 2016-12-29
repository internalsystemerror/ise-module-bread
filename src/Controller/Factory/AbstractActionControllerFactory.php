<?php

namespace Ise\Bread\Controller\Factory;

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
        $class = trim($controllerName, '\\');
        if (substr($class, -10) === 'Controller') {
            $class = substr($class, 0, -10);
        }
        
        return str_replace('\\Controller\\', '\\Service\\', $class);
    }
}
