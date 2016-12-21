<?php

namespace IseBread\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceAbstractFactory implements AbstractFactoryInterface
{
    
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $mapperClass        = $this->translateServiceToMapper($container, $requestedName);
        $formElementManager = $container->get('FormElementManager');
        $mapper             = $container->get($mapperClass);
        
        return new $requestedName($formElementManager, $mapper);
    }
    
    /**
     * {@inheritDoc}
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return (bool) $this->translateServiceToMapper($container, $requestedName);
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this($serviceLocator, $requestedName);
    }
    
    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return $this->canCreate($serviceLocator, $requestedName);
    }

    /**
     * Translate service name to mapper class
     *
     * @param  string $serviceName Service name to translate to mapper class
     * @return boolean
     */
    protected function translateServiceToMapper(ServiceLocatorInterface $serviceLocator, $serviceName)
    {
        $mapperClass = trim(str_replace('Service', 'Mapper', substr($serviceName, 0, strrpos($serviceName, 'Service'))), '\\');
        if (!$mapperClass) {
            return false;
        }
        
        return $serviceLocator->has($mapperClass) ? $mapperClass : false;
    }
}
