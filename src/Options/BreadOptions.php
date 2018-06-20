<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Options;

use Ise\Bread\Exception\InvalidArgumentException;
use Ise\Bread\Factory\BreadDoctrineOrmMapperFactory;
use Ise\Bread\Factory\BreadServiceFactory;
use Ise\Bread\Factory\FormAbstractFactory;
use Ise\Bread\Mapper\DoctrineOrm\BreadMapper;
use Ise\Bread\Service\BreadService;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

class BreadOptions extends AbstractOptions
{

    /**
     * @var array[]
     */
    protected $serviceManagerOptions = ['factories' => [BreadService::class => BreadServiceFactory::class,],];

    /**
     * @var array[]
     */
    protected $mapperManagerOptions = ['factories' => [BreadMapper::class => BreadDoctrineOrmMapperFactory::class,],];

    /**
     * @var array[]
     */
    protected $formManagerOptions = ['abstract_factories' => [FormAbstractFactory::class,],];

    /**
     * @var array
     */
    protected $entityDefaults = [];

    /**
     * @var ControllerOptions[]
     */
    protected $controllerOptions = [];

    /**
     * @var ServiceOptions[]
     */
    protected $serviceOptions = [];

    /**
     * @var MapperOptions[]
     */
    protected $mapperOptions = [];

    /**
     * @var EntityOptions[]
     */
    protected $entityOptions = [];

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        $array      = parent::toArray();
        $properties = ['controllerOptions', 'serviceOptions', 'mapperOptions', 'entityOptions'];
        foreach ($properties as $property) {
            foreach ($array[$property] as $key => $value) {
                /** @var AbstractFactoryClassOptions $value */
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
    public function getServiceManagerOptions(): array
    {
        return $this->serviceManagerOptions;
    }

    /**
     * Set service manager options
     *
     * @param array[] $serviceManagerOptions
     *
     * @return void
     */
    public function setServiceManagerOptions(array $serviceManagerOptions): void
    {
        $this->serviceManagerOptions = ArrayUtils::merge($this->serviceManagerOptions, $serviceManagerOptions);
    }

    /**
     * Get mapper manager options
     *
     * @return array[]
     */
    public function getMapperManagerOptions(): array
    {
        return $this->mapperManagerOptions;
    }

    /**
     * Set mapper manager options
     *
     * @param array[] $mapperManagerOptions
     *
     * @return void
     */
    public function setMapperManagerOptions(array $mapperManagerOptions): void
    {
        $this->mapperManagerOptions = ArrayUtils::merge($this->mapperManagerOptions, $mapperManagerOptions);
    }

    /**
     * Get form manager options
     *
     * @return array[]
     */
    public function getFormManagerOptions(): array
    {
        return $this->formManagerOptions;
    }

    /**
     * Set form manager options
     *
     * @param array[] $formManagerOptions
     *
     * @return void
     */
    public function setFormManagerOptions(array $formManagerOptions): void
    {
        $this->formManagerOptions = ArrayUtils::merge($this->formManagerOptions, $formManagerOptions);
    }

    /**
     * Get entity defaults
     *
     * @return array
     */
    public function getEntityDefaults(): array
    {
        return $this->entityDefaults;
    }

    /**
     * Set entity defaults
     *
     * @param array $defaults
     *
     * @return void
     */
    public function setEntityDefaults(array $defaults): void
    {
        $this->entityDefaults = $defaults;
    }

    /**
     * Get controller options for entity class
     *
     * @param string $entityClass
     *
     * @return ControllerOptions
     */
    public function getControllerOptionsForEntity(string $entityClass): ControllerOptions
    {
        foreach ($this->controllerOptions as $controller) {
            if ($controller->getEntityClass() === $entityClass) {
                return $controller;
            }
        }

        return null;
    }

    /**
     * Get controller options
     *
     * @param string $class
     *
     * @return ControllerOptions|null
     */
    public function getControllerOptions(string $class): ?ControllerOptions
    {
        if (!$this->controllerOptions[$class]) {
            return null;
        }

        return $this->controllerOptions[$class];
    }

    /**
     * Set controller options
     *
     * @param string       $class
     * @param array|string $options
     *
     * @return void
     */
    public function setControllerOptions(string $class, $options): void
    {
        $this->parseClassOptions($class, $options);
        $this->setPropertyOptions($this->controllerOptions, ControllerOptions::class, $class, $options);
    }

    /**
     * Get all controller options
     *
     * @return ControllerOptions[]
     */
    public function getAllControllerOptions(): array
    {
        return $this->controllerOptions;
    }

    /**
     * Set multiple controller options
     *
     * @param iterable $controllerOptions
     *
     * @return void
     */
    public function setManyControllerOptions(iterable $controllerOptions): void
    {
        foreach ($controllerOptions as $class => $options) {
            $this->setControllerOptions($class, $options);
        }
    }

    /**
     * Get service options
     *
     * @param string $class
     *
     * @return ServiceOptions|null
     */
    public function getServiceOptions(string $class): ?ServiceOptions
    {
        if (!$this->serviceOptions[$class]) {
            return null;
        }

        return $this->serviceOptions[$class];
    }

    /**
     * Set service options
     *
     * @param string       $class
     * @param string|array $options
     *
     * @return void
     */
    public function setServiceOptions(string $class, $options): void
    {
        $this->parseClassOptions($class, $options);
        $this->setPropertyOptions($this->serviceOptions, ServiceOptions::class, $class, $options);
    }

    /**
     * Get all service options
     *
     * @return ServiceOptions[]
     */
    public function getAllServiceOptions(): array
    {
        return $this->serviceOptions;
    }

    /**
     * Set multiple service options
     *
     * @param iterable $serviceOptions
     *
     * @return void
     */
    public function setManyServiceOptions(iterable $serviceOptions): void
    {
        foreach ($serviceOptions as $class => $options) {
            $this->setServiceOptions($class, $options);
        }
    }

    /**
     * Get mapper options
     *
     * @param string $class
     *
     * @return MapperOptions|null
     */
    public function getMapperOptions(string $class): ?MapperOptions
    {
        if (!$this->mapperOptions[$class]) {
            return null;
        }

        return $this->mapperOptions[$class];
    }

    /**
     * Set mapper options
     *
     * @param string       $class
     * @param string|array $options
     *
     * @return void
     */
    public function setMapperOptions(string $class, $options): void
    {
        $this->parseClassOptions($class, $options);
        $this->setPropertyOptions($this->mapperOptions, MapperOptions::class, $class, $options);
    }

    /**
     * Get all mapper options
     *
     * @return MapperOptions[]
     */
    public function getAllMapperOptions(): array
    {
        return $this->mapperOptions;
    }

    /**
     * Set multiple mapper options
     *
     * @param iterable $mapperOptions
     *
     * @return void
     */
    public function setManyMapperOptions(iterable $mapperOptions): void
    {
        foreach ($mapperOptions as $class => $options) {
            $this->setMapperOptions($class, $options);
        }
    }

    /**
     * Get entity options
     *
     * @param string $class
     *
     * @return EntityOptions|null
     */
    public function getEntityOptions(string $class): ?EntityOptions
    {
        if (!$this->entityOptions[$class]) {
            return null;
        }

        return $this->entityOptions[$class];
    }

    /**
     * Set entity options
     *
     * @param string       $class
     * @param string|array $options
     *
     * @return void
     */
    public function setEntityOptions(string $class, $options): void
    {
        $this->parseClassOptions($class, $options);
        $options = ArrayUtils::merge($this->getEntityDefaults(), $options);
        $this->setPropertyOptions($this->entityOptions, EntityOptions::class, $class, $options);
    }

    /**
     * Get all entity options
     *
     * @return EntityOptions[]
     */
    public function getAllEntityOptions(): array
    {
        return $this->entityOptions;
    }

    /**
     * Set multiple entity options
     *
     * @param iterable $entities
     *
     * @return void
     */
    public function setManyEntityOptions(iterable $entities): void
    {
        foreach ($entities as $class => $options) {
            $this->setEntityOptions($class, $options);
        }
    }

    /**
     * Parse class options
     *
     * @param string       $class
     * @param string|array $options
     *
     * @return void
     */
    private function parseClassOptions(string &$class, &$options): void
    {
        // Check options is an array
        if (is_string($options)) {
            $class   = $options;
            $options = [];
        }
        if (!is_array($options)) {
            throw new InvalidArgumentException(sprintf(
                'Options must be either a string or array, %s given.',
                is_object($options) ? get_class($options) : gettype($options)
            ));
        }
    }

    /**
     * Set class options for a class property
     *
     * @param array  $property
     * @param string $optionsClass
     * @param string $class
     * @param array  $options
     *
     * @return void
     */
    private function setPropertyOptions(array &$property, string $optionsClass, string $class, array $options): void
    {
        // Create entity options
        if (!$property[$class]) {
            $property[$class] = new $optionsClass;
        }

        // Set new entity options
        if (!$options['class']) {
            $options['class'] = $class;
        }
        $property[$class]->setFromArray($options);
    }
}
