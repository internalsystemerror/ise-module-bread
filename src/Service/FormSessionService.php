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
        $this->sessionContainer[self::KEY_MESSAGES] = (array) $form->getMessages();
        $this->sessionContainer[self::KEY_DATA]     = (array) $form->getInputFilter()->getRawValues();
    }

    /**
     * Load form from session
     *
     * @param Form $form
     */
    public function loadForm(Form $form)
    {
        if (isset($this->sessionContainer[self::KEY_MESSAGES])) {
            $form->setMessages($this->sessionContainer[self::KEY_MESSAGES]);
            unset($this->sessionContainer[self::KEY_MESSAGES]);
        }
        if (isset($this->sessionContainer[self::KEY_DATA])) {
            $form->setData($this->sessionContainer[self::KEY_DATA]);
            unset($this->sessionContainer[self::KEY_DATA]);
        }
    }
}
