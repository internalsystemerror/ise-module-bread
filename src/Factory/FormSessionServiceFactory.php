<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Factory;

use Interop\Container\ContainerInterface;
use Ise\Bread\Service\FormSessionService;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\Container;

class FormSessionServiceFactory implements FactoryInterface
{

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): FormSessionService
    {
        $sessionContainer = new Container(FormSessionService::class);
        $sessionContainer->setExpirationHops(1);
        return new $requestedName($sessionContainer);
    }
}
