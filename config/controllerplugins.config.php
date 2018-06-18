<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread;

return [
    'aliases'   => [
        'formSession' => Controller\Plugin\FormSessionPlugin::class,
    ],
    'factories' => [
        Controller\Plugin\FormSessionPlugin::class => Controller\Plugin\Factory\FormSessionPluginFactory::class,
    ],
];
