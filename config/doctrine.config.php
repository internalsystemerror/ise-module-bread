<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
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
                __DIR__ . '/../src/Entity',
            ],
        ],
        'orm_default'             => [
            'drivers' => [
                __NAMESPACE__ . '\Entity' => 'bread_annotation_driver',
            ],
        ],
    ],
];
