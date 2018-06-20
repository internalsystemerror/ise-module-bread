<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

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
     * @inheritdoc
     * @throws \ReflectionException
     */
    public function setBaseClass(string $class): void
    {
        $this->classImplementsInterface($class, ServiceInterface::class);
        parent::setBaseClass($class);
    }

    /**
     * Get forms
     *
     * @return string[]
     */
    public function getForms(): array
    {
        return $this->forms;
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
     *
     * @return void
     */
    public function setForms(array $forms): void
    {
        $this->forms = ArrayUtils::merge($this->forms, $forms);
    }
}
