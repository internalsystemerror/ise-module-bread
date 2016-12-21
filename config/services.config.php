<?php

namespace IseBread;

use IseBread\Factory\FormAbstractFactory;
use IseBread\Factory\ServiceAbstractFactory;
use Zend\Cache\Service\StorageCacheAbstractServiceFactory;

// Return config
return [
    'abstract_factories' => [
        FormAbstractFactory::class,
        ServiceAbstractFactory::class,
        StorageCacheAbstractServiceFactory::class,
    ],
];
