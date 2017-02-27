<?php

namespace Ise\Bread\ServiceManager;

use Zend\Form\FormInterface;

class FormPluginManager extends AbstractPluginManager
{

    /**
     * @var string
     */
    protected $instanceOf = FormInterface::class;

}
