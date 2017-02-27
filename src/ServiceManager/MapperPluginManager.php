<?php

namespace Ise\Bread\ServiceManager;

use Ise\Bread\Mapper\MapperInterface;

class MapperPluginManager extends AbstractPluginManager
{

    /**
     * @var string
     */
    protected $instanceOf = MapperInterface::class;

}
