<?php

namespace Ise\Bread;

return [
    'ise'                => [
        'bread' => [],
    ],
    'controller_plugins' => [
        'aliases'   => [
            'formSession' => Controller\Plugin\FormSessionPlugin::class,
        ],
        'factories' => [
            Controller\Plugin\FormSessionPlugin::class => Controller\Plugin\Factory\FormSessionPluginFactory::class,
        ],
    ],
    'doctrine_factories' => ['formannotationbuilder' => Factory\AnnotationBuilderFactory::class,],
    'view_manager'       => include __DIR__ . '/views.config.php',
    'doctrine'           => include __DIR__ . '/doctrine.config.php',
    'service_manager'    => include __DIR__ . '/services.config.php',
];
