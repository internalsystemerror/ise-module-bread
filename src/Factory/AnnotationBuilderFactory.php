<?php

namespace Ise\Bread\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Ise\Bread\Form\Annotation\AnnotationBuilder;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AnnotationBuilderFactory implements FactoryInterface
{
    
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (!$requestedName) {
            $requestedName = AnnotationBuilder::class;
        }
        return new $requestedName($container->get(EntityManager::class));
    }

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this($serviceLocator, $requestedName);
    }
}
