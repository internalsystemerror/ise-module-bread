<?php

namespace Ise\Bread;

return [
    'doctrine_factories' => ['formannotationbuilder' => Factory\AnnotationBuilderFactory::class,],
    'controller_plugins' => include __DIR__ . '/controllerplugins.config.php',
    'view_manager'       => include __DIR__ . '/views.config.php',
    'doctrine'           => include __DIR__ . '/doctrine.config.php',
    'service_manager'    => include __DIR__ . '/services.config.php',
];
