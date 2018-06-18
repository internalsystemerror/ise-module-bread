<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Options;

use Ise\Bread\Factory\BreadDoctrineOrmMapperFactory;
use Ise\Bread\Factory\BreadServiceFactory;
use Ise\Bread\Factory\FormAbstractFactory;
use Ise\Bread\Mapper\BreadMapper;
use Ise\Bread\Service\BreadService;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

class BreadOptions extends AbstractOptions
{

    /**
     * @var array[]
     */
    protected $serviceManager = [
        'factories' => [
            BreadService::class => BreadServiceFactory::class,
        ],
    ];

    /**
     * @var array[]
     */
    protected $mapperManager = [
        'factories' => [
            BreadMapper::class => BreadDoctrineOrmMapperFactory::class,
        ],
    ];

    /**
     * @var array[]
     */
    protected $formManager = [
        'abstract_factories' => [
            FormAbstractFactory::class,
        ],
    ];

    /**
     * @var array
     */
    protected $entityDefaults = [];

    /**
     * @var ControllerOptions[]
     */
    protected $controllers = [];

    /**
     * @var ServiceOptions[]
     */
    protected $services = [];

    /**
     * @var MapperOptions[]
     */
    protected $mappers = [];

    /**
     * @var EntityOptions[]
     */
    protected $entities = [];

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        $array      = parent::toArray();
        $properties = ['controllers', 'services', 'mappers', 'entities'];
        foreach ($properties as $property) {
            foreach ($array[$property] as $key => $value) {
                $array[$property][$key] = $value->toArray();
            }
        }

        return $array;
    }

    /**
     * Get service manager options
     *
     * @return array[]
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager options
     *
     * @param array[] $serviceManager
     *
     * @return self
     */
    public function setServiceManager(array $serviceManager)
    {
        $this->serviceManager = ArrayUtils::merge($this->serviceManager, $serviceManager);
        return $this;
    }

    /**
     * Get mapper manager options
     *
     * @return array[]
     */
    public function getMapperManager()
    {
        return $this->mapperManager;
    }

    /**
     * Set mapper manager options
     *
     * @param array[] $mapperManager
     *
     * @return self
     */
    public function setMapperManager(array $mapperManager)
    {
        $this->mapperManager = ArrayUtils::merge($this->mapperManager, $mapperManager);
        return $this;
    }

    /**
     * Get form manager options
     *
     * @return array[]
     */
    public function getFormManager()
    {
        return $this->formManager;
    }

    /**
     * Set form manager options
     *
     * @param array[] $formManager
     *
     * @return self
     */
    public function setFormManager(array $formManager)
    {
        $this->formManager = ArrayUtils::merge($this->formManager, $formManager);
        return $this;
    }

    /**
     * Get entity defaults
     *
     * @return array
     */
    public function getEntityDefaults()
    {
        return $this->entityDefaults;
    }

    /**
     * Set entity defaults
     *
     * @param array $defaults
     *
     * @return self
     */
    public function setEntityDefaults(array $defaults)
    {
        $this->entityDefaults = $defaults;
        return $this;
    }

    /**
     * Set controller options
     *
     * @param string $class
     * @param array  $options
     *
     * @return self
     */
    public function setController($class, array $options)
    {
        // Check options is an array
        if (is_string($options)) {
            $class   = $options;
            $options = [];
        }

        // Create controller options
        if (!isset($this->controllers[$class])) {
            $this->controllers[$class] = new ControllerOptions;
        }

        // Set new controller options
        if (!isset($options['class'])) {
            $options['class'] = $class;
        }
        $this->controllers[$class]->setFromArray($options);
        return $this;
    }

    /**
     * Get controller options for entity class
     *
     * @param string $entityClass
     *
     * @return ControllerOptions
     */
    public function getControllerForEntity($entityClass)
    {
        foreach ($this->controllers as $controller) {
            if ($controller->getEntityClass() === $entityClass) {
                return $controller;
            }
        }
    }

    /**
     * Get controller options
     *
     * @param string $class
     *
     * @return ControllerOptions
     */
    public function getController($class)
    {
        if (!isset($this->controllers[$class])) {
            return;
        }
        return $this->controllers[$class];
    }

    /**
     * Get all controller options
     *
     * @return ControllerOptions[]
     */
    public function getControllers()
    {
        return $this->controllers;
    }

    /**
     * Set multiple controller options
     *
     * @param array[] $controllers
     *
     * @return self
     */
    public function setControllers(array $controllers)
    {
        foreach ($controllers as $class => $options) {
            $this->setController($class, $options);
        }
        return $this;
    }

    /**
     * Set service options
     *
     * @param string       $class
     * @param string|array $options
     *
     * @return self
     */
    public function setService($class, array $options)
    {
        // Check options is an array
        if (is_string($options)) {
            $class   = $options;
            $options = [];
        }

        // Create service options
        if (!isset($this->services[$class])) {
            $this->services[$class] = new ServiceOptions;
        }

        // Set new service options
        $this->services[$class]->setFromArray($options);
        return $this;
    }

    /**
     * Get service options
     *
     * @param string $class
     *
     * @return ServiceOptions
     */
    public function getService($class)
    {
        if (!isset($this->services[$class])) {
            return;
        }
        return $this->services[$class];
    }

    /**
     * Get all service options
     *
     * @return ServiceOptions[]
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Set multiple service options
     *
     * @param array[] $services
     *
     * @return self
     */
    public function setServices(array $services)
    {
        foreach ($services as $class => $options) {
            $this->setService($class, $options);
        }
        return $this;
    }

    /**
     * Set mapper options
     *
     * @param string       $class
     * @param string|array $options
     *
     * @return self
     */
    public function setMapper($class, $options)
    {
        // Check options is an array
        if (is_string($options)) {
            $class   = $options;
            $options = [];
        }

        // Create mapper options
        if (!isset($this->mappers[$class])) {
            $this->mappers[$class] = new MapperOptions;
        }

        // Set new mapper options
        $this->mappers[$class]->setFromArray($options);
        return $this;
    }

    /**
     * Get mapper options
     *
     * @param string $class
     *
     * @return MapperOptions
     */
    public function getMapper($class)
    {
        if (!isset($this->mappers[$class])) {
            return;
        }
        return $this->mappers[$class];
    }

    /**
     * Get all mapper options
     *
     * @return MapperOptions[]
     */
    public function getMappers()
    {
        return $this->mappers;
    }

    /**
     * Set multiple mapper options
     *
     * @param array[] $mappers
     *
     * @return self
     */
    public function setMappers(array $mappers)
    {
        foreach ($mappers as $class => $options) {
            $this->setMapper($class, $options);
        }
        return $this;
    }

    /**
     * Set entity options
     *
     * @param string       $class
     * @param string|array $options
     *
     * @return self
     */
    public function setEntity($class, $options)
    {
        // Check options is an array
        if (is_string($options)) {
            $class   = $options;
            $options = $this->getEntityDefaults();
        } else {
            $options = ArrayUtils::merge($this->getEntityDefaults(), $options);
        }

        // Create entity options
        if (!isset($this->entities[$class])) {
            $this->entities[$class] = new EntityOptions;
        }

        // Set new entity options
        if (!isset($options['class'])) {
            $options['class'] = $class;
        }
        $this->entities[$class]->setFromArray($options);
        return $this;
    }

    /**
     * Get entity options
     *
     * @param string $class
     *
     * @return EntityOptions
     */
    public function getEntity($class)
    {
        return $this->entities[$class];
    }

    /**
     * Get all entity options
     *
     * @return EntityOptions[]
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * Set multiple entity options
     *
     * @param array[] $entities
     *
     * @return self
     */
    public function setEntities(array $entities)
    {
        foreach ($entities as $class => $options) {
            $this->setEntity($class, $options);
        }
        return $this;
    }
}
