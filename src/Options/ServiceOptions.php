<?php

namespace Ise\Bread\Options;

use Ise\Bread\EventManager\BreadEvent;
use Ise\Bread\Factory\BreadServiceFactory;
use Ise\Bread\Service\BreadService;
use Ise\Bread\Service\ServiceInterface;
use Zend\Stdlib\ArrayUtils;

class ServiceOptions extends AbstractFactoryClassOptions
{

    /**
     * @var string
     */
    protected $baseClass = BreadService::class;

    /**
     * @var string
     */
    protected $factory = BreadServiceFactory::class;

    /**
     * @var string[]
     */
    protected $forms = [
        BreadEvent::FORM_CREATE => '',
        BreadEvent::FORM_UPDATE => '',
        BreadEvent::FORM_DIALOG => '',
    ];

    /**
     * {@inheritDoc}
     */
    public function setBaseClass($class)
    {
        $this->classImplementsInterface($class, ServiceInterface::class);
        return parent::setBaseClass($class);
    }

    /**
     * Set forms
     *
     * An array of forms: [
     *     BreadEvent::FORM_CREATE => 'Module\Form\Create\Alias',
     *     BreadEvent::FORM_UPDATE => 'Module\Form\Update\Alias',
     *     BreadEvent::FORM_DIALOG => 'Module\Form\Dialog\Alias',
     * ];
     *
     * @param string[] $forms
     * @return self
     */
    public function setForms(array $forms)
    {
        $this->forms = ArrayUtils::merge($this->forms, $forms);
        return $this;
    }

    /**
     * Get forms
     *
     * @return string[]
     */
    public function getForms()
    {
        return $this->forms;
    }
}
