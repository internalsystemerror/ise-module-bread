<?php

namespace Ise\Bread;

use Ise\Bread\Factory\FormAbstractFactory;
use Ise\Bread\Factory\ServiceAbstractFactory;
use Zend\Cache\Service\StorageCacheAbstractServiceFactory;

// Return config
return [
    'abstract_factories' => [
        FormAbstractFactory::class,
        ServiceAbstractFactory::class,
        StorageCacheAbstractServiceFactory::class,
    ],
];
