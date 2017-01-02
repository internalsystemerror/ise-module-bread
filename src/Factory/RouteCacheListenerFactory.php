<?php

namespace Ise\Bread\Factory;

use Interop\Container\ContainerInterface;
use Ise\Bread\Listener\RouteCacheListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RouteCacheListenerFactory implements FactoryInterface
{

    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $cacheService = $container->get(RouteCacheListener::CACHE_SERVICE);
        return new $requestedName($cacheService);
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
    
        return $this($serviceLocator, $requestedName);
    }
}
