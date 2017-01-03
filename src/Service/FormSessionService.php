<?php

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
    public function __invoke()
    {
        return $this->sessionContainer;
    }

    /**
     * Save form to session
     *
     * @param Form $form
     */
    public function saveForm(Form $form)
    {
        $name = $form->getName();
        if (!isset($this->sessionContainer[$name])){ 
            $this->sessionContainer[$name] = [];
        }
        $this->sessionContainer[$name][self::KEY_MESSAGES] = (array) $form->getMessages();
        $this->sessionContainer[$name][self::KEY_DATA]     = (array) $form->getInputFilter()->getRawValues();
    }

    /**
     * Load form from session
     *
     * @param Form $form
     */
    public function loadForm(Form $form)
    {
        $name = $form->getName();
        if (isset($this->sessionContainer[$name][self::KEY_MESSAGES])) {
            $form->setMessages($this->sessionContainer[$name][self::KEY_MESSAGES]);
            unset($this->sessionContainer[$name][self::KEY_MESSAGES]);
        }
        if (isset($this->sessionContainer[$name][self::KEY_DATA])) {
            $form->setData($this->sessionContainer[$name][self::KEY_DATA]);
            unset($this->sessionContainer[$name][self::KEY_DATA]);
        }
    }
}
