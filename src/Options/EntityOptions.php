<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Options;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Ise\Bread\Entity\EntityInterface;

class EntityOptions extends AbstractClassOptions
{

    /**
     * @var string
     */
    protected $hydrator = DoctrineObject::class;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $mapper;

    /**
     * @var string
     */
    protected $service;

    /**
     * Set class
     *
     * @param string $class
     *
     * @return void
     * @throws \ReflectionException
     */
    public function setClass(string $class): void
    {
        $this->classImplementsInterface($class, EntityInterface::class);
    }

    /**
     * Get hydrator class
     *
     * @return string
     */
    public function getHydrator(): string
    {
        return $this->hydrator;
    }

    /**
     * Set hydrator class
     *
     * @param string $hydrator
     *
     * @return void
     */
    public function setHydrator(string $hydrator): void
    {
        $this->hydrator = $hydrator;
    }

    /**
     * Get mapper
     *
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * Set controller
     *
     * @param string $controller
     *
     * @return void
     */
    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * Get mapper
     *
     * @return string
     */
    public function getMapper(): string
    {
        return $this->mapper;
    }

    /**
     * Set mapper
     *
     * @param string $mapper
     *
     * @return void
     */
    public function setMapper(string $mapper): void
    {
        $this->mapper = $mapper;
    }

    /**
     * Get service
     *
     * @return string
     */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * Set service
     *
     * @param string $service
     *
     * @return void
     */
    public function setService(string $service): void
    {
        $this->service = $service;
    }
}
