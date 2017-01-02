<?php

namespace Ise\Bread;

use Ise\Bread\Factory\FormAbstractFactory;
use Ise\Bread\Factory\FormSessionServiceFactory;
use Ise\Bread\Factory\ServiceAbstractFactory;
use Ise\Bread\Factory\RouteCacheListenerFactory;
use Ise\Bread\Listener\RouteCacheListener;
use Ise\Bread\Service\FormSessionService;
use Zend\Cache\Service\StorageCacheAbstractServiceFactory;

// Return config
return [
    'factories' => [
        RouteCacheListener::class => RouteCacheListenerFactory::class,
        FormSessionService::class => FormSessionServiceFactory::class,
    ],
    'abstract_factories' => [
        FormAbstractFactory::class,
        ServiceAbstractFactory::class,
        StorageCacheAbstractServiceFactory::class,
    ],
];
