<?php

namespace Ise\Bread;

return [
    'aliases'   => [
        'formSession' => Controller\Plugin\FormSessionPlugin::class,
    ],
    'factories' => [
        Controller\Plugin\FormSessionPlugin::class => Controller\Plugin\Factory\FormSessionPluginFactory::class,
    ],
];
