<?php

namespace Ise\Bread\Factory;

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
        $mapperClass = $this->translateServiceToMapper($container, $requestedName);
        $mapper      = $container->get($mapperClass);

        return new $requestedName($container, $mapper);
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
     * @param  ContainerInterface $container   Service manager
     * @param  string             $serviceName Service name to translate to mapper class
     * @return boolean
     */
    protected function translateServiceToMapper(ContainerInterface $container, $serviceName)
    {
        if (strpos($serviceName, '\\Service\\') === false) {
            return false;
        }

        $class = trim($serviceName, '\\');
        if (substr($class, -7) === 'Service') {
            $class = substr($class, 0, -7);
        }

        $mapperClass = str_replace('\\Service\\', '\\Mapper\\', $class);
        if (!$mapperClass || !$container->has($mapperClass)) {
            return false;
        }

        return $mapperClass;
    }
}
