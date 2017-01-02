<?php

namespace Ise\Bread\Form\Annotation;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use DoctrineModule\Validator\NoObjectExists;
use DoctrineModule\Validator\UniqueObject;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Ise\Bread\Router\Http\BreadRouteStack;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;

class ElementAnnotationsListener extends AbstractListenerAggregate
{

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Constructor
     * 
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager, $actionType = null)
    {
        $this->objectManager = $objectManager;
        $this->actionType    = $actionType;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 10)
    {
        $this->listeners[] = $events->attach(
            AnnotationBuilder::EVENT_CONFIGURE_FIELD, [$this, 'handleIdentifierFields'], $priority + 1
        );
        $this->listeners[] = $events->attach(
            AnnotationBuilder::EVENT_CONFIGURE_FIELD, [$this, 'handleUniqueFields'], $priority
        );
    }

    /**
     * Handle identifier fields
     * 
     * @param EventInterface $event
     * @return void
     */
    public function handleIdentifierFields(EventInterface $event)
    {

        $name     = $event->getParam('name');
        $metadata = $event->getParam('metadata');
        if (!$metadata || !$metadata->hasField($name)) {
            return;
        }
        $mapping = $metadata->getFieldMapping($name);
        if (!$mapping || !isset($mapping['id']) || !$mapping['id']) {
            return;
        }

        $elementSpec = $event->getParam('elementSpec');
        if (!$elementSpec) {
            $event->setParam('elementSpec', []);
            $elementSpec = $event->getParam('elementSpec');
        }

        if (!isset($elementSpec['spec'])) {
            $elementSpec['spec'] = [];
        }

        $elementSpec['spec']['type'] = 'hidden';
    }

    /**
     * Handle unique fields
     * 
     * @param EventInterface $event
     */
    public function handleUniqueFields(EventInterface $event)
    {
        $name     = $event->getParam('name');
        $metadata = $event->getParam('metadata');
        if (!$metadata || !$metadata->hasField($name)) {
            return;
        }
        $mapping = $metadata->getFieldMapping($name);
        if (!$mapping || !$mapping['unique']) {
            return;
        }

        switch ($this->actionType) {
            case BreadRouteStack::ACTION_CREATE:
                return $this->addNoObjectExistsValidator($event, $metadata, $mapping);
            default:
                return $this->addUniqueObjectValidator($event, $metadata, $mapping);
        }
    }

    /**
     * Add NoObjectExists validation
     * 
     * @param EventInterface $event
     * @param ClassMetadata $metadata
     * @param array $mapping
     */
    public function addNoObjectExistsValidator(EventInterface $event, ClassMetadata $metadata, array $mapping)
    {
        $inputSpec = $event->getParam('inputSpec');
        if (!$inputSpec) {
            $event->setParam('inputSpec', []);
            $inputSpec = $event->getParam('inputSpec');
        }

        if (!isset($inputSpec['validators'])) {
            $inputSpec['validators'] = [];
        }

        $inputSpec['validators'][] = [
            'name'    => NoObjectExists::class,
            'options' => [
                'object_repository' => $this->objectManager->getRepository($metadata->getName()),
                'fields'            => [$mapping['fieldName'],],
                'messageTemplates'  => [
                    NoObjectExists::ERROR_OBJECT_FOUND => 'That value is already taken',
                ],
            ],
        ];
    }

    /**
     * Add UniqueObject validation
     * 
     * @param EventInterface $event
     * @param ClassMetadata $metadata
     * @param array $mapping
     */
    public function addUniqueObjectValidator(EventInterface $event, ClassMetadata $metadata, array $mapping)
    {
        $inputSpec = $event->getParam('inputSpec');
        if (!$event->getParam('inputSpec')) {
            $event->setParam('inputSpec', []);
            $inputSpec = $event->getParam('inputSpec');
        }

        if (!isset($inputSpec['validators'])) {
            $inputSpec['validators'] = [];
        }

        $inputSpec['validators'][] = [
            'name'    => UniqueObject::class,
            'options' => [
                'object_manager'    => $this->objectManager,
                'object_repository' => $this->objectManager->getRepository($metadata->getName()),
                'fields'            => [$mapping['fieldName'],],
                'use_context'       => true,
                'messageTemplates'  => [
                    UniqueObject::ERROR_OBJECT_NOT_UNIQUE => 'That value is already taken',
                ],
            ],
        ];
    }
}
