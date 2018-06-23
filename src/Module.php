<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;

class Module implements ConfigProviderInterface, InitProviderInterface
{

    /**
     * @inheritdoc
     */
    public function init(ModuleManagerInterface $moduleManager): void
    {
        $eventManager = $moduleManager->getEventManager();

        $configListener = new Listener\ConfigListener;
        $configListener->attach($eventManager);
    }

    /**
     * @inheritdoc
     */
    public function getConfig(): array
    {
        return require __DIR__ . '/../config/module.config.php';
    }
}
