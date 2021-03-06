<?php

namespace Ise\Bread\Controller\Plugin;

use Ise\Bread\Service\FormSessionService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class FormSessionPlugin extends AbstractPlugin
{
    
    /**
     * @var FormSessionService
     */
    protected $formSessionService;
    
    /**
     * Constructor
     *
     * @param FormSessionService $formSessionService
     */
    public function __construct(FormSessionService $formSessionService)
    {
        $this->formSessionService = $formSessionService;
    }
    
    /**
     * Invoke
     *
     * @return FormSessionService;
     */
    public function __invoke()
    {
        return $this->formSessionService;
    }
}
