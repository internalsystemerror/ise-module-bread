<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Service;

use Ise\Bread\Entity\EntityInterface;
use Ise\Bread\EventManager\BreadEvent;
use Ise\Bread\Exception\InvalidArgumentException;
use Ise\Bread\Mapper\MapperInterface;
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
     * @param BreadManager             $breadManager
     * @param MapperInterface          $mapper
     * @param string[]|FormInterface[] $forms
     */
    public function __construct(BreadManager $breadManager, MapperInterface $mapper, array $forms)
    {
        $this->breadManager = $breadManager;
        $this->mapper       = $mapper;
        $this->forms        = $forms;
    }

    /**
     * @inheritdoc
     */
    public function browse(array $criteria = [], array $orderBy = [], $limit = null, $offset = null): array
    {
        return $this->mapper->browse($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function read($id): ?EntityInterface
    {
        return $this->mapper->read($id);
    }

    /**
     * @inheritdoc
     */
    public function readBy(array $criteria): ?EntityInterface
    {
        return $this->mapper->readBy($criteria);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function add(array $data): ?EntityInterface
    {
        // Validate form
        $entity = $this->validateForm(BreadEvent::FORM_CREATE, $data);
        if (!$entity) {
            return null;
        }

        // Save entity
        $this->mapper->add($entity);
        return $entity;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function edit(array $data): ?EntityInterface
    {
        // Validate form
        $entity = $this->validateForm(BreadEvent::FORM_UPDATE, $data);
        if (!$entity) {
            return null;
        }

        // Save entity
        $entity->setLastModified(new \DateTime);
        $this->mapper->edit($entity);
        return $entity;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function delete(array $data): ?EntityInterface
    {
        // Validate form
        $entity = $this->validateForm(BreadEvent::FORM_DIALOG, $data);
        if (!$entity) {
            return null;
        }

        // Save entity
        $this->mapper->delete($entity);
        return $entity;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function disable(array $data): ?EntityInterface
    {
        // Validate form
        $entity = $this->validateForm(BreadEvent::FORM_DIALOG, $data);
        if (!$entity) {
            return null;
        }

        // Save entity
        $entity->setDisabled(true);
        $entity->setLastModified(new \DateTime);
        $this->mapper->edit($entity);
        return $entity;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function enable(array $data): ?EntityInterface
    {
        // Validate form
        $entity = $this->validateForm(BreadEvent::FORM_DIALOG, $data);
        if (!$entity) {
            return null;
        }

        // Save entity
        $entity->setDisabled(false);
        $entity->setLastModified(new \DateTime);
        $this->mapper->edit($entity);
        return $entity;
    }

    /**
     * Get a form by name
     *
     * @param string $action
     *
     * @return FormInterface
     * @throws InvalidArgumentException
     */
    public function getForm(string $action): FormInterface
    {
        if (!$this->forms[$action]) {
            throw new InvalidArgumentException(sprintf(
                'Invalid form name given, "%s"',
                $action
            ));
        }
        if (!$this->forms[$action] instanceof FormInterface) {
            $this->forms[$action] = $this->breadManager->getForm($this->forms[$action]);
        }
        return $this->forms[$action];
    }

    /**
     * Validate form method
     *
     * @param  string $action Action to perform
     * @param  array  $data   Data to act upon
     *
     * @return EntityInterface|null
     */
    protected function validateForm(string $action, array $data): ?EntityInterface
    {
        $form = $this->getForm($action);
        $form->setData($data);
        if (!$form->isValid()) {
            return null;
        }

        $entity = $form->getData();
        if (!$entity instanceof EntityInterface) {
            throw new InvalidArgumentException(sprintf(
                '%s must implement %s',
                is_object($entity) ? get_class($entity) : gettype($entity),
                EntityInterface::class
            ));
        }

        return $entity;
    }

    /**
     * Add a form error message
     *
     * @param FormInterface $form
     * @param array         $newMessages
     *
     * @return void
     */
    protected function addFormMessage(FormInterface $form, array $newMessages): void
    {
        $currentMessages = $form->getMessages();
        $form->setMessages(array_merge($currentMessages, $newMessages));
    }
}
