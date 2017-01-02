<?php

namespace Ise\Bread\Form\Annotation;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
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
    public function attach(EventManagerInterface $events, $priority = -10)
    {
        $this->listeners[] = $events->attach(
            AnnotationBuilder::EVENT_CONFIGURE_FIELD,
            [$this, 'handleIdentifierField'],
            $priority - 1
        );
        $this->listeners[] = $events->attach(
            AnnotationBuilder::EVENT_CONFIGURE_FIELD,
            [$this, 'handleUniqueFields'],
            $priority
        );
    }
    
    public function handleIdentifierField(EventInterface $event)
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
        
        if ($name === 'id'){
//            var_dump($event);
//            var_dump($mapping);
//            var_dump($metadata);
        }
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
            case BreadRouteStack::ACTION_UPDATE:
                return $this->addNoObjectExistsValidator($event, $metadata, $mapping);
            default:
                return;
        }
    }
    
    public function addNoObjectExistsValidator($event, ClassMetadata $metadata, array $mapping)
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
            'name'    => 'DoctrineModule\Validator\UniqueObject',
            'options' => [
                'object_manager'    => $this->objectManager,
                'object_repository' => $this->objectManager->getRepository($metadata->getName()),
                'fields' => [$mapping['fieldName'],],
                'use_context' => true,
            ],
        ];
    }
    
    public function addObjectExistsValidator($event, ClassMetadata $metadata, array $mapping)
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
            'name'    => 'DoctrineModule\Validator\UniqueObject',
            'options' => [
                'object_manager'    => $this->objectManager,
                'object_repository' => $this->objectManager->getRepository($metadata->getName()),
                'fields' => [$mapping['fieldName'],],
                'use_context' => true,
            ],
        ];
    }
}
