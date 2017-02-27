<?php

namespace Ise\Bread\ServiceManager;

use Ise\Bread\Service\ServiceInterface;

class ServicePluginManager extends AbstractPluginManager
{

    /**
     * @var string
     */
    protected $instanceOf = ServiceInterface::class;

}
