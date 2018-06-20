<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\ServiceManager;

use Ise\Bread\Service\ServiceInterface;
use Zend\ServiceManager\AbstractPluginManager;

class ServicePluginManager extends AbstractPluginManager
{

    /**
     * @var string
     */
    protected $instanceOf = ServiceInterface::class;
}
