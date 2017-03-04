<?php

namespace Ise\Bread\EventManager;

use Ise\Bread\Entity\EntityInterface;
use Zend\EventManager\Event;
use Zend\View\Model\ViewModel;

class BreadEvent extends Event
{
    
    /**
     * The identifier key
     */
    const IDENTIFIER     = 'id';
    
    /**
     * Available bread actions
     */
    const ACTION_INDEX   = 'browse';
    const ACTION_READ    = 'read';
    const ACTION_CREATE  = 'add';
    const ACTION_UPDATE  = 'edit';
    const ACTION_DELETE  = 'delete';
    const ACTION_ENABLE  = 'enable';
    const ACTION_DISABLE = 'disable';
    
    /**
     * Available bread forms
     */
    const FORM_CREATE = self::ACTION_CREATE;
    const FORM_UPDATE = self::ACTION_UPDATE;
    const FORM_DIALOG = 'dialog';
    
    /**
     * Bread events triggered by event manager
     */
    const EVENT_INDEX  = self::ACTION_INDEX;
    const EVENT_READ   = self::ACTION_READ;
    const EVENT_CREATE = self::FORM_CREATE;
    const EVENT_UPDATE = self::FORM_UPDATE;
    const EVENT_DIALOG = self::FORM_DIALOG;
    
    /**
     * @var string[]
     */
    protected static $availableActions = [
        self::ACTION_READ,
        self::ACTION_CREATE,
        self::ACTION_UPDATE,
        self::ACTION_DELETE,
        self::ACTION_ENABLE,
        self::ACTION_DISABLE,
    ];
    
    /**
     * @var string[]
     */
    protected static $availableForms = [
        self::FORM_CREATE,
        self::FORM_UPDATE,
        self::FORM_DIALOG,
    ];
    
    /**
     * @var string
     */
    protected $action;
    
    /**
     * @var EntityInterface
     */
    protected $entity;
    
    /**
     * @var string|FormInterface
     */
    protected $form;
    
    /**
     * @var boolean|array
     */
    protected $prgData;
    
    /**
     * Get the list of available bread actions
     * 
     * @return string[]
     */
    public static function getAvailableActions()
    {
        return static::$availableActions;
    }
    
    /**
     * Get the list of available forms
     * 
     * @return string[]
     */
    public static function getAvailableForms()
    {
        return static::$availableForms;
    }
    
    /**
     * Set the action
     * 
     * @param string $action
     * @return self
     */
    public function setAction($action)
    {
        $this->action = (string) $action;
        return $this;
    }
    
    /**
     * Get the action
     * 
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * Set the entity
     * 
     * @param EntityInterface $entity
     * @return self
     */
    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
        return $this;
    }
    
    /**
     * Get the entity
     * 
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }
    
    /**
     * Set the form
     * 
     * @param string|FormInterface $form
     * @return self
     */
    public function setForm($form)
    {
        $this->form = $form;
        return $this;
    }
    
    /**
     * Get the form
     * 
     * @return string|FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }
    
    /**
     * Set the PRG data
     * 
     * @param boolean|array $data
     * @return self
     */
    public function setPrgData($data)
    {
        $this->prgData = $data;
        return $this;
    }
    
    /**
     * Get the PRG data
     * 
     * @return boolean|array
     */
    public function getPrgData()
    {
        return $this->prgData;
    }
    
    /**
     * Set the view model
     * 
     * @param ViewModel $viewModel
     * @return self
     */
    public function setViewModel(ViewModel $viewModel)
    {
        $this->viewModel = $viewModel;
        return $this;
    }
    
    /**
     * Get the view model
     * 
     * @return ViewModel
     */
    public function getViewModel()
    {
        return $this->viewModel;
    }
}