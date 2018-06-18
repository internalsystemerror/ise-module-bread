<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Controller\Factory;

use Interop\Container\ContainerInterface;
use Ise\Bread\Controller\ControllerInterface;
use Ise\Bread\EventManager\BreadEventManager;
use Ise\Bread\Options\ControllerOptions;
use Ise\Bread\ServiceManager\BreadManager;
use Zend\EventManager\SharedEventManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class BreadActionControllerFactory implements FactoryInterface
{

    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return $this->createControllerFactory(
            $container,
            $requestedName,
            new BreadEventManager($container->get(SharedEventManager::class))
        );
    }

    /**
     * Create controller factory method
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param BreadEventManager  $breadEventManager
     *
     * @return ControllerInterface
     */
    protected function createControllerFactory(
        ContainerInterface $container,
        $requestedName,
        BreadEventManager $breadEventManager
    ) {
        /** @var BreadManager $breadManager */
        $breadManager      = $container->get(BreadManager::class);
        $controllerClass   = $breadManager->getControllerBaseClass($requestedName);
        $controllerOptions = $breadManager->getControllerOptionsFromControllerClass($requestedName);
        $entityClass       = $controllerOptions->getEntityClass();

        $breadEventManager->setIdentifiers(['Ise\Bread', $entityClass, $controllerClass]);
        return $this->createController(
            $breadEventManager,
            $breadManager,
            $requestedName,
            $controllerClass,
            $controllerOptions
        );
    }

    /**
     * Create controller instance
     *
     * @param BreadEventManager $breadEventManager
     * @param BreadManager      $breadManager
     * @param string            $requestedName
     * @param string            $controllerClass
     * @param ControllerOptions $controllerOptions
     *
     * @return ControllerInterface
     */
    protected function createController(
        BreadEventManager $breadEventManager,
        BreadManager $breadManager,
        $requestedName,
        $controllerClass,
        ControllerOptions $controllerOptions
    ) {
        return new $controllerClass(
            $breadEventManager,
            $breadManager->getService($breadManager->getServiceClassFromControllerClass($requestedName)),
            $controllerOptions
        );
    }
}
