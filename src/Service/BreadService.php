<?php

namespace Ise\Bread\Service;

use DateTime;
use Ise\Bread\Entity\EntityInterface;
use Ise\Bread\Exception\InvalidArgumentException;
use Ise\Bread\Mapper\MapperInterface;
use Ise\Bread\Router\Http\Bread;
use Ise\Bread\ServiceManager\BreadManager;
use Zend\Form\FormInterface;

class BreadService implements ServiceInterface
{

    /**
     * @var BreadManager
     */
    protected $breadManager;

    /**
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * @var string[]|FormInterface[]
     */
    protected $forms;

    /**
     * Constructor
     *
     * @param BreadManager $breadManager
     * @param MapperInterface $mapper
     * @param string[]|FormInterface[] $forms
     */
    public function __construct(BreadManager $breadManager, MapperInterface $mapper, array $forms)
    {
        $this->breadManager = $breadManager;
        $this->mapper       = $mapper;
        $this->forms        = $forms;
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
        // Validate form
        $entity = $this->validateForm(Bread::FORM_CREATE, $data);
        if (!$entity) {
            return false;
        }

        // Save entity
        return $this->mapper->add($entity);
    }

    /**
     * {@inheritDoc}
     */
    public function edit(array $data)
    {
        // Validate form
        $entity = $this->validateForm(Bread::FORM_UPDATE, $data);
        if (!$entity) {
            return false;
        }

        // Save entity
        $entity->setLastModified(new DateTime);
        return $this->mapper->edit($entity);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(array $data)
    {
        // Validate form
        $entity = $this->validateForm(Bread::FORM_DIALOG, $data);
        if (!$entity) {
            return false;
        }

        // Save entity
        return $this->mapper->delete($entity);
    }

    /**
     * {@inheritDoc}
     */
    public function disable(array $data)
    {
        // Validate form
        $entity = $this->validateForm(Bread::FORM_DIALOG, $data);
        if (!$entity) {
            return false;
        }

        // Save entity
        $entity->setDisabled(true);
        $entity->setLastModified(new DateTime);
        return $this->mapper->edit($entity);
    }

    /**
     * {@inheritDoc}
     */
    public function enable(array $data)
    {
        // Validate form
        $entity = $this->validateForm(Bread::FORM_DIALOG, $data);
        if (!$entity) {
            return false;
        }

        // Save entity
        $entity->setDisabled(false);
        $entity->setLastModified(new DateTime);
        return $this->mapper->edit($entity);
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
        if (!isset($this->forms[$action])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid form name given, "%s"', $action
            ));
        }
        if (is_string($this->forms[$action])) {
            $this->forms[$action] = $this->breadManager->getForm($this->forms[$action]);
        }
        return $this->forms[$action];
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

        return $form->getData();
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
