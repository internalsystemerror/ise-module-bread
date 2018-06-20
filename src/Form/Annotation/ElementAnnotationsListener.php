<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Form\Annotation;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use DoctrineModule\Validator\NoObjectExists;
use DoctrineModule\Validator\UniqueObject;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Ise\Bread\EventManager\BreadEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Validator\Uuid;

class ElementAnnotationsListener extends AbstractListenerAggregate
{

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string|null
     */
    protected $actionType;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     * @param string|null   $actionType
     */
    public function __construct(ObjectManager $objectManager, string $actionType = null)
    {
        $this->objectManager = $objectManager;
        $this->actionType    = $actionType;
    }

    /**
     * @inheritdoc
     */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(
            AnnotationBuilder::EVENT_CONFIGURE_FIELD,
            [$this, 'handleIdentifierFields'],
            $priority
        );
        $this->listeners[] = $events->attach(
            AnnotationBuilder::EVENT_CONFIGURE_FIELD,
            [$this, 'handleUniqueFields'],
            $priority
        );
    }

    /**
     * Handle identifier fields
     *
     * @param EventInterface $event
     *
     * @return void
     */
    public function handleIdentifierFields(EventInterface $event): void
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

        // Hide element
        $elementSpec                 = $this->getEventElementSpec($event);
        $elementSpec['spec']['type'] = 'hidden';

        // Add validation
        $inputSpec = $this->getEventInputSpec($event);
        switch ($metadata->getTypeOfField($name)) {
            case 'guid':
                $inputSpec['validators'][] = ['name' => Uuid::class,];
                break;
        }
    }

    /**
     * Handle unique fields
     *
     * @param EventInterface $event
     *
     * @return void
     */
    public function handleUniqueFields(EventInterface $event): void
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
            case BreadEvent::ACTION_CREATE:
                return $this->addNoObjectExistsValidator($event, $metadata, $mapping);
            default:
                return $this->addUniqueObjectValidator($event, $metadata, $mapping);
        }
    }

    /**
     * Get event element spec
     *
     * @param EventInterface $event
     *
     * @return array
     */
    protected function getEventElementSpec(EventInterface $event): array
    {
        $elementSpec = $event->getParam('elementSpec');
        if (!$elementSpec) {
            $event->setParam('elementSpec', []);
            $elementSpec = $event->getParam('elementSpec');
        }
        if (!isset($elementSpec['spec'])) {
            $elementSpec['spec'] = [];
        }
        return $elementSpec;
    }

    /**
     * Get event input spec
     *
     * @param EventInterface $event
     *
     * @return array
     */
    protected function getEventInputSpec(EventInterface $event): array
    {
        $inputSpec = $event->getParam('inputSpec');
        if (!$inputSpec) {
            $event->setParam('inputSpec', []);
            $inputSpec = $event->getParam('inputSpec');
        }
        if (!isset($inputSpec['validators'])) {
            $inputSpec['validators'] = [];
        }
        return $inputSpec;
    }

    /**
     * Add NoObjectExists validation
     *
     * @param EventInterface $event
     * @param ClassMetadata  $metadata
     * @param array          $mapping
     *
     * @return void
     */
    protected function addNoObjectExistsValidator(EventInterface $event, ClassMetadata $metadata, array $mapping): void
    {
        $inputSpec                 = $this->getEventInputSpec($event);
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
     * @param ClassMetadata  $metadata
     * @param array          $mapping
     *
     * @return void
     */
    protected function addUniqueObjectValidator(EventInterface $event, ClassMetadata $metadata, array $mapping): void
    {
        $inputSpec                 = $this->getEventInputSpec($event);
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
