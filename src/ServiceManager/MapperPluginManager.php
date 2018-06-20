<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\ServiceManager;

use Ise\Bread\Mapper\MapperInterface;
use Zend\ServiceManager\AbstractPluginManager;

class MapperPluginManager extends AbstractPluginManager
{

    /**
     * @var string
     */
    protected $instanceOf = MapperInterface::class;
}
