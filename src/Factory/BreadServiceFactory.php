<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Factory;

use Interop\Container\ContainerInterface;
use Ise\Bread\Service\BreadService;
use Ise\Bread\ServiceManager\BreadManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class BreadServiceFactory implements FactoryInterface
{

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): BreadService
    {
        $breadManager = $container->get(BreadManager::class);
        $serviceClass = $breadManager->getServiceBaseClass($requestedName);

        return new $serviceClass(
            $breadManager,
            $breadManager->getMapper($breadManager->getMapperClassFromServiceClass($requestedName)),
            $breadManager->getFormsFromServiceClass($requestedName)
        );
    }
}
