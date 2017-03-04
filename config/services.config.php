<?php

namespace Ise\Bread;

return [
    'factories' => [
        EventManager\BreadEventManager::class      => Factory\BreadEventManagerFactory::class,
        Service\FormSessionService::class          => Factory\FormSessionServiceFactory::class,
        ServiceManager\BreadManager::class         => Factory\BreadManagerFactory::class,
        ServiceManager\FormPluginManager::class    => Factory\FormPluginManagerFactory::class,
        ServiceManager\MapperPluginManager::class  => Factory\MapperPluginManagerFactory::class,
        ServiceManager\ServicePluginManager::class => Factory\ServicePluginManagerFactory::class,
    ],
];
