<?php

namespace Ise\Bread\EventManager;

use Zend\EventManager\EventManager;

class BreadEventManager extends EventManager
{

    /**
     * @var string
     */
    protected $eventClass = BreadEvent::class;
}
