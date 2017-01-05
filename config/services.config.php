<?php

namespace Ise\Bread;

return [
    'factories'          => [
        Service\FormSessionService::class => Factory\FormSessionServiceFactory::class,
    ],
    'abstract_factories' => [
        Factory\FormAbstractFactory::class,
        Factory\ServiceAbstractFactory::class,
    ],
];
