<?php

namespace Ise\Bread\Factory;

use Interop\Container\ContainerInterface;
use Ise\Bread\Options\BreadOptions;
use Ise\Bread\ServiceManager\FormPluginManager;
use Ise\Bread\ServiceManager\MapperPluginManager;
use Ise\Bread\ServiceManager\ServicePluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BreadManagerFactory implements FactoryInterface
{
    
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        return new $requestedName(
            $container->get(ServicePluginManager::class),
            $container->get(MapperPluginManager::class),
            $container->get(FormPluginManager::class),
            new BreadOptions($config['ise']['bread'])
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this($serviceLocator, $requestedName);
    }
}
