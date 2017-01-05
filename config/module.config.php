<?php

namespace Ise\Bread;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Ise\Bread\Controller\Plugin\FormSessionPlugin;
use Ise\Bread\Controller\Plugin\Factory\FormSessionPluginFactory;
use Ise\Bread\DBAL\Types\DateIntervalType;
use Ise\Bread\Factory\AnnotationBuilderFactory;
use Ise\Bread\Router\Http\BreadRouteStack;
use Ise\Bread\Router\Http\Bread;

return [
    'controller_plugins' => [
        'aliases'   => [
            'formSession' => FormSessionPlugin::class,
        ],
        'factories' => [
            FormSessionPlugin::class => FormSessionPluginFactory::class,
        ],
    ],
    'doctrine'      => [
        'configuration' => [
            'orm_default' => [
                'types' => [
                    'dateinterval' => DateIntervalType::class,
                ],
            ],
        ],
        'connection'    => [
            'orm_default' => [
                'doctrine_type_mappings' => [
                    'dateinterval' => 'dateinterval',
                    'enum'         => 'string',
                    'set'          => 'string',
                ],
            ],
        ],
        'driver'        => [
            'bread_annotation_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity'
                ],
            ],
            'orm_default'             => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => 'bread_annotation_driver',
                ],
            ],
        ],
    ],
    'doctrine_factories' => [
        'formannotationbuilder' => AnnotationBuilderFactory::class,
    ],
    'router'        => [
        'router_class' => BreadRouteStack::class,
    ],
    'route_manager' => [
        'invokables' => [
            'bread' => Bread::class,
        ],
    ],
    'view_manager'  => [
        'controller_map'      => [
            'Ise' => true,
        ],
        'template_map'        => [
            'ise/bread/bread/_dialogue' => __DIR__ . '/../view/ise/bread/bread/_dialogue.phtml',
            'ise/bread/bread/browse'    => __DIR__ . '/../view/ise/bread/bread/browse.phtml',
            'ise/bread/bread/read'      => __DIR__ . '/../view/ise/bread/bread/read.phtml',
            'ise/bread/bread/add'       => __DIR__ . '/../view/ise/bread/bread/add.phtml',
            'ise/bread/bread/edit'      => __DIR__ . '/../view/ise/bread/bread/edit.phtml',
            'ise/bread/bread/delete'    => __DIR__ . '/../view/ise/bread/bread/delete.phtml',
            'ise/bread/bread/enable'    => __DIR__ . '/../view/ise/bread/bread/enable.phtml',
            'ise/bread/bread/disable'   => __DIR__ . '/../view/ise/bread/bread/disable.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
