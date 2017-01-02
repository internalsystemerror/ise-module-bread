<?php

namespace Ise\Bread;

use DoctrineORMModule\Form\Annotation\AnnotationBuilder as DoctrineAnnotationBuilder;
use Ise\Bread\Factory\AnnotationBuilderFactory;
use Ise\Bread\Factory\FormAbstractFactory;
use Ise\Bread\Factory\FormSessionServiceFactory;
use Ise\Bread\Factory\ServiceAbstractFactory;
use Ise\Bread\Factory\RouteCacheListenerFactory;
use Ise\Bread\Form\Annotation\AnnotationBuilder;
use Ise\Bread\Listener\RouteCacheListener;
use Ise\Bread\Service\FormSessionService;
use Zend\Cache\Service\StorageCacheAbstractServiceFactory;

// Return config
return [
    'aliases'   => [
        DoctrineAnnotationBuilder::class => AnnotationBuilder::class,
    ],
    'factories' => [
        AnnotationBuilder::class  => AnnotationBuilderFactory::class,
        RouteCacheListener::class => RouteCacheListenerFactory::class,
        FormSessionService::class => FormSessionServiceFactory::class,
    ],
    'abstract_factories' => [
        FormAbstractFactory::class,
        ServiceAbstractFactory::class,
        StorageCacheAbstractServiceFactory::class,
    ],
];
