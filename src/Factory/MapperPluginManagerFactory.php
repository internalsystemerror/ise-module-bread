<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Factory;

use Interop\Container\ContainerInterface;
use Ise\Bread\ServiceManager\MapperPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class MapperPluginManagerFactory implements FactoryInterface
{

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): MapperPluginManager
    {
        $config = $container->get('Config');
        return new $requestedName(
            $container,
            $config['ise']['bread']['mapper_manager']
        );
    }
}
