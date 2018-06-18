<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\ServiceManager;

use Zend\Form\FormInterface;

class FormPluginManager extends AbstractPluginManager
{

    /**
     * @var string
     */
    protected $instanceOf = FormInterface::class;
}
