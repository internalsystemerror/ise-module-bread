<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread;

return [
    'controller_map' => [
        'Ise' => true,
    ],
    'template_map'   => [
        'ise/bread/bread/browse' => realpath(__DIR__ . '/../view/ise/bread/bread/browse.phtml'),
        'ise/bread/bread/read'   => realpath(__DIR__ . '/../view/ise/bread/bread/read.phtml'),
        'ise/bread/bread/add'    => realpath(__DIR__ . '/../view/ise/bread/bread/add.phtml'),
        'ise/bread/bread/edit'   => realpath(__DIR__ . '/../view/ise/bread/bread/edit.phtml'),
        'ise/bread/bread/dialog' => realpath(__DIR__ . '/../view/ise/bread/bread/dialog.phtml'),
        'partial/dialog'         => realpath(__DIR__ . '/../view/partial/dialog.phtml'),
    ],
];
