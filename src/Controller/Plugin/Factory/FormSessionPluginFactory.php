<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Controller\Plugin\Factory;

use Interop\Container\ContainerInterface;
use Ise\Bread\Service\FormSessionService;
use Zend\ServiceManager\Factory\FactoryInterface;

class FormSessionPluginFactory implements FactoryInterface
{

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new $requestedName($container->get(FormSessionService::class));
    }
}
