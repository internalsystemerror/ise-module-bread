<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\EventManager;

use Zend\EventManager\EventManager;

class BreadEventManager extends EventManager
{

    /**
     * @var string
     */
    protected $eventClass = BreadEvent::class;
}
