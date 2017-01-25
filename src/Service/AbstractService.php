<?php

namespace Ise\Bread\Service;

use DateTime;
use Interop\Container\ContainerInterface;
use Ise\Bread\Entity\EntityInterface;
use Ise\Bread\Exception\InvalidArgumentException;
use Ise\Bread\Mapper\MapperInterface;
use Ise\Bread\Router\Http\Bread;
use Zend\Form\FormInterface;

abstract class AbstractService implements ServiceInterface
{

    /**
     * @var ContainerInterface
     */
    protected $serviceLocator;

    /**
     * @var string[]|FormInterface[]
     */
    protected $form = [
        Bread::ACTION_CREATE  => '',
        Bread::ACTION_UPDATE  => '',
        Bread::ACTION_DELETE  => '',
        Bread::ACTION_ENABLE  => '',
        Bread::ACTION_DISABLE => '',
    ];

    /**
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * @var string
     */
    protected $entityClass;
    
    /**
     * Constructor
     *
     * @param ContainerInterface $serviceLocator
     * @param MapperInterface    $mapper
     */
    public function __construct(ContainerInterface $serviceLocator, MapperInterface $mapper)
    {
        $this->serviceLocator = $serviceLocator;
        $this->mapper         = $mapper;
    }

    /**
     * {@inheritDoc}
     */
    public function browse(array $criteria = [], array $orderBy = [], $limit = null, $offset = null)
    {
        return $this->mapper->browse($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function read($id)
    {
        return $this->mapper->read($id);
    }
    
    /**
     * {@inheritDoc}
     */
    public function readBy(array $criteria)
    {
        return $this->mapper->readBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function add(array $data)
    {
        return $this->aed(Bread::ACTION_CREATE, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function edit(array $data)
    {
        return $this->aed(Bread::ACTION_UPDATE, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(array $data)
    {
        return $this->aed(Bread::ACTION_DELETE, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function disable(array $data)
    {
        // Validate form
        $entity = $this->validateForm(Bread::ACTION_DISABLE, $data);
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
        $entity = $this->validateForm(Bread::ACTION_ENABLE, $data);
        if (!$entity) {
            return false;
        }

        // Set disabled
        $entity->setDisabled(false);

        // Save entity
        return $this->mapper->enable($entity);
    }

    /**
     * Get a form by name
     * 
     * @param type $action
     * @return type
     * @throws InvalidArgumentException
     */
    public function getForm($action)
    {
        if (!isset($this->form[$action])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid form name given, "%s"',
                $action
            ));
        }
        if (is_string($this->form[$action])) {
            $form                = $this->serviceLocator->get($this->form[$action]);
            $this->form[$action] = $form;
        }
        return $this->form[$action];
    }

    /**
     * Add/edit/delete method
     *
     * @param  string $action Action to perform
     * @param  array  $data   Data to act upon
     * @return boolean|EntityInterface
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
     * @return boolean|EntityInterface
     */
    protected function validateForm($action, array $data)
    {
        $form = $this->getForm($action);
        $form->setData($data);
        if (!$form->isValid()) {
            return false;
        }

        $entity = $form->getData();
        if ($action !== Bread::ACTION_CREATE) {
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
