<?php

namespace Ise\Bread\Factory;

use Ise\Bread\Service\FormSessionService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class FormSessionServiceFactory implements FactoryInterface
{
    
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sessionContainer = new Container(FormSessionService::class);
        $sessionContainer->setExpirationHops(1);
        return new $requestedName($sessionContainer);
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this($serviceLocator, $requestedName);
    }
}
