<?php

namespace Ise\Bread;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'controller_plugins' => [
        'aliases'   => [
            'formSession' => Controller\Plugin\FormSessionPlugin::class,
        ],
        'factories' => [
            Controller\Plugin\FormSessionPlugin::class => Controller\Plugin\Factory\FormSessionPluginFactory::class,
        ],
    ],
    'doctrine'           => [
        'configuration' => [
            'orm_default' => [
                'types' => [
                    'dateinterval' => DBAL\Types\DateIntervalType::class,
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
        'formannotationbuilder' => Factory\AnnotationBuilderFactory::class,
    ],
    'view_manager'       => [
        'controller_map'      => [
            'Ise' => true,
        ],
        'template_map'        => [
            'ise/bread/bread/browse'   => __DIR__ . '/../view/ise/bread/bread/browse.phtml',
            'ise/bread/bread/read'     => __DIR__ . '/../view/ise/bread/bread/read.phtml',
            'ise/bread/bread/add'      => __DIR__ . '/../view/ise/bread/bread/add.phtml',
            'ise/bread/bread/edit'     => __DIR__ . '/../view/ise/bread/bread/edit.phtml',
            'ise/bread/bread/dialogue' => __DIR__ . '/../view/ise/bread/bread/dialogue.phtml',
            'partial/dialogue'         => __DIR__ . '/../view/partial/dialogue.phtml',
        ],
    ],
];
