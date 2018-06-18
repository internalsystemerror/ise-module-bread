<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Options;

use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity;
use Ise\Bread\Entity\EntityInterface;

class EntityOptions extends AbstractClassOptions
{

    /**
     * @var string
     */
    protected $hydrator = DoctrineEntity::class;

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
     * @return self
     */
    public function setClass($class)
    {
        $this->classImplementsInterface($class, EntityInterface::class);
        return parent::setClass($class);
    }

    /**
     * Get hydrator class
     *
     * @return string
     */
    public function getHydrator()
    {
        return $this->hydrator;
    }

    /**
     * Set hydrator class
     *
     * @param string $hydrator
     *
     * @return self
     */
    public function setHydrator($hydrator)
    {
        $this->hydrator = (string)$hydrator;
        return $this;
    }

    /**
     * Get mapper
     *
     * @return string|array
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set controller
     *
     * @param string|array $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * Get mapper
     *
     * @return string|array
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * Set mapper
     *
     * @param string|array $mapper
     *
     * @return self
     */
    public function setMapper($mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }

    /**
     * Get service
     *
     * @return string|array
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set service
     *
     * @param string|array $service
     *
     * @return self
     */
    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }
}
