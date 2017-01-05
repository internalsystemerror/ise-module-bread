<?php

namespace Ise\Bread;

use DoctrineORMModule\Form\Annotation\AnnotationBuilder as DoctrineAnnotationBuilder;

// Return config
return [
    'aliases'            => [
        DoctrineAnnotationBuilder::class => Form\Annotation\AnnotationBuilder::class,
    ],
    'factories'          => [
        Form\Annotation\AnnotationBuilder::class => Factory\AnnotationBuilderFactory::class,
        Service\FormSessionService::class        => Factory\FormSessionServiceFactory::class,
    ],
    'abstract_factories' => [
        Factory\FormAbstractFactory::class,
        Factory\ServiceAbstractFactory::class,
    ],
];
