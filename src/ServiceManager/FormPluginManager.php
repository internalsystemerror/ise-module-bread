<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\ServiceManager;

use Zend\Form\FormInterface;
use Zend\ServiceManager\AbstractPluginManager;

class FormPluginManager extends AbstractPluginManager
{

    /**
     * @var string
     */
    protected $instanceOf = FormInterface::class;
}
