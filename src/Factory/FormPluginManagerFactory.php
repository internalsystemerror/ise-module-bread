<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Factory;

use Interop\Container\ContainerInterface;
use Ise\Bread\ServiceManager\FormPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class FormPluginManagerFactory implements FactoryInterface
{

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FormPluginManager
    {
        $config = $container->get('Config');
        return new $requestedName(
            $container,
            $config['ise']['bread']['form_manager']
        );
    }
}
