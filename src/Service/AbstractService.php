<?php

namespace IseBread\Service;

use DateTime;
use IseBread\Mapper\MapperInterface;
use IseBread\Mvc\Router\Http\BreadRouteStack;
use Zend\Form\FormInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractService implements ServiceInterface
{

    /**
     * @var ServiceLocatorInterface
     */
    protected $formElementManager;

    /**
     * @var string[]|FormInterface[]
     */
    protected $form = [
        'add'     => '',
        'edit'    => '',
        'delete'  => '',
        'enable'  => '',
        'disable' => '',
    ];

    /**
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * @var string
     */
    protected $entityClass = '';
    
    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $formElementManager
     * @param MapperInterface         $mapper
     */
    public function __construct(ServiceLocatorInterface $formElementManager, MapperInterface $mapper)
    {
        $this->formElementManager = $formElementManager;
        $this->mapper             = $mapper;
    }

    /**
     * {@inheritDoc}
     */
    public function browse()
    {
        return $this->mapper->browse();
    }

    /**
     * {@inheritDoc}
     */
    public function read($id)
    {
        return $this->mapper->read($id);
    }

    /**
     * {@inheritDoc}
     */
    public function add(array $data)
    {
        return $this->aed('add', $data);
    }

    /**
     * {@inheritDoc}
     */
    public function edit(array $data)
    {
        return $this->aed('edit', $data);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(array $data)
    {
        return $this->aed('delete', $data);
    }

    /**
     * {@inheritDoc}
     */
    public function disable(array $data)
    {
        // Validate form
        $entity = $this->validateForm('disable', $data);
        if (!$entity) {
            return false;
        }

        // Get entity
        $entity->setDisabled(true);

        // Save entity
        return $this->mapper->disable($entity);
    }

    /**
     * {@inheritDoc}
     */
    public function enable(array $data)
    {
        // Validate form
        $entity = $this->validateForm('enable', $data);
        if (!$entity) {
            return false;
        }

        // Set disabled
        $entity->setDisabled(false);

        // Save entity
        return $this->mapper->enable($entity);
    }

    /**
     * {@inheritDoc}
     */
    public function getForm($action)
    {
        if (!isset($this->form[$action])) {
            throw new Exception\InvalidArgumentException('Invalid form name given, "' . $action . '"');
        }
        if (is_string($this->form[$action])) {
            $form                = $this->formElementManager->getServiceLocator()->get($this->form[$action]);
            $this->form[$action] = $form;
        }
        return $this->form[$action];
    }

    /**
     * Add/edit/delete method
     *
     * @param  string $action Action to perform
     * @param  array  $data   Data to act upon
     * @return boolean|object
     */
    protected function aed($action, array $data)
    {
        // Validate form
        $entity = $this->validateForm($action, $data);
        if (!$entity) {
            return false;
        }

        // Save entity
        return $this->mapper->$action($entity);
    }

    /**
     * Validate form method
     *
     * @param  string $action Action to perform
     * @param  array  $data   Data to act upon
     * @return boolean|object
     */
    protected function validateForm($action, array $data)
    {
        $form = $this->getForm($action);
        $form->setData($data);
        if (!$form->isValid()) {
            return false;
        }

        $entity = $form->getData();
        if ($action !== BreadRouteStack::ACTION_CREATE) {
            $entity->setLastModified(new DateTime);
        }

        return $entity;
    }

    /**
     * Add a form error message
     *
     * @param FormInterface $form
     * @param array         $newMessages
     */
    protected function addFormMessage(FormInterface $form, $newMessages)
    {
        $currentMessages = $form->getMessages();
        $form->setMessages(array_merge($currentMessages, $newMessages));
    }
}
