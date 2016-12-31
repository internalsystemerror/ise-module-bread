<?php

namespace Ise\Bot\Form\Annotation;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
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
            AnnotationBuilder::EVENT_CONFIGURE_FIELD,
            [$this, 'handleUniqueFields'],
            $priority
        );
    }
    
    /**
     * Handle unique fields
     * 
     * @param EventInterface $event
     */
    protected function handleUniqueFields(EventInterface $event)
    {
        $mapping  = $this->getFieldMapping($event);
        $metadata = $event->getParam('metadata');
        $name     = $event->getParam('name');
        if ($name === 'name') {
        }
    }
}
