<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Service;

use Zend\Form\Form;
use Zend\Session\Container;

class FormSessionService
{

    const KEY_MESSAGES = 'messages';
    const KEY_DATA     = 'data';

    /**
     * @var Container
     */
    protected $sessionContainer;

    /**
     * Constructor
     *
     * @param Container $sessionContainer
     */
    public function __construct(Container $sessionContainer)
    {
        $this->sessionContainer = $sessionContainer;
    }

    /**
     * Invoke this service
     *
     * @return Container
     */
    public function __invoke(): Container
    {
        return $this->sessionContainer;
    }

    /**
     * Save form to session
     *
     * @param Form $form
     */
    public function saveForm(Form $form): void
    {
        $name = $form->getName();
        if (!$this->sessionContainer[$name]) {
            $this->sessionContainer[$name] = [];
        }
        $this->sessionContainer[$name][self::KEY_MESSAGES] = $form->getMessages();

        $inputFilter = $form->getInputFilter();
        if ($inputFilter) {
            $this->sessionContainer[$name][self::KEY_DATA] = $inputFilter->getRawValues();
        }
    }

    /**
     * Load form from session
     *
     * @param Form $form
     *
     * @return void
     */
    public function loadForm(Form $form): void
    {
        $name = $form->getName();
        if ($this->sessionContainer[$name][self::KEY_MESSAGES]) {
            $form->setMessages($this->sessionContainer[$name][self::KEY_MESSAGES]);
            unset($this->sessionContainer[$name][self::KEY_MESSAGES]);
        }
        if ($this->sessionContainer[$name][self::KEY_DATA]) {
            $form->setData($this->sessionContainer[$name][self::KEY_DATA]);
            unset($this->sessionContainer[$name][self::KEY_DATA]);
        }
    }
}
