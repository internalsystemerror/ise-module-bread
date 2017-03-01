<?php

namespace Ise\Bread\ServiceManager;

use Ise\Bread\Options\BreadOptions;

class BreadManager
{

    /**
     * @var ServicePluginManager
     */
    protected $serviceManager;

    /**
     * @var MapperPluginManager
     */
    protected $mapperManager;

    /**
     * @var FormPluginManager
     */
    protected $formManager;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string[]
     */
    protected $entityToService;

    /**
     * @var string[]
     */
    protected $mapperToEntity;

    /**
     * @var string[]
     */
    protected $serviceToMapper;
    
    /**
     * @var string[]
     */
    protected $serviceToEntity;

    /**
     * @var string[]
     */
    protected $serviceToForms;

    /**
     * @var string[]
     */
    protected $controllerToService;

    /**
     * @var string[]
     */
    protected $controllerToOptions;

    /**
     * Constructor
     *
     * @param ServicePluginManager $serviceManager
     * @param MapperPluginManager $mapperManager
     * @param FormPluginManager $formManager
     * @param BreadOptions $options
     */
    public function __construct(ServicePluginManager $serviceManager, MapperPluginManager $mapperManager, FormPluginManager $formManager, BreadOptions $options)
    {
        // Set properties
        $this->serviceManager = $serviceManager;
        $this->mapperManager  = $mapperManager;
        $this->formManager    = $formManager;
        $this->options        = $options;

        // Create entity map
        foreach ($this->options->getEntities() as $entity) {
            $this->entityToService[$entity->getClass()]   = $entity->getService();
            $this->entityToMapper[$entity->getClass()]    = $entity->getMapper();
            
            $this->serviceToEntity[$entity->getService()] = $entity->getClass();
            $this->serviceToMapper[$entity->getService()] = $entity->getMapper();
            
            $this->mapperToEntity[$entity->getMapper()]   = $entity->getClass();
        }

        // Create controller map
        foreach ($this->options->getControllers() as $controller) {
            $this->controllerToService[$controller->getClass()] = $this->entityToService[$controller->getEntityClass()];
            $this->controllerToOptions[$controller->getClass()] = $controller;
        }
        
        // Create service map
        foreach ($this->options->getServices() as $service) {
            $this->serviceToForms[$service->getClass()]  = $service->getForms();
        }
    }
    
    /**
     * Get entity
     *
     * @param string $entityClass
     * @return array[]
     */
    public function getEntityOptions($entityClass)
    {
        return $this->options->getEntity($entityClass);
    }
    
    /**
     * Get controller base class
     *
     * @param string $requestedName
     * @return string
     */
    public function getControllerBaseClass($requestedName)
    {
        return $this->options->getController($requestedName)->getBaseClass();
    }
    
    /**
     * Get service base class
     *
     * @param string $requestedName
     * @return string
     */
    public function getServiceBaseClass($requestedName)
    {
        return $this->options->getService($requestedName)->getBaseClass();
    }
    
    /**
     * Get mapper base class
     *
     * @param string $requestedName
     * @return string
     */
    public function getMapperBaseClass($requestedName)
    {
        return $this->options->getMapper($requestedName)->getBaseClass();
    }

    /**
     * Get service
     *
     * @param string $serviceClass
     * @return ServiceInterface
     */
    public function getService($serviceClass)
    {
        return $this->serviceManager->get($serviceClass);
    }

    /**
     * Get mapper
     *
     * @param string $mapperClass
     * @return MapperInterface
     */
    public function getMapper($mapperClass)
    {
        return $this->mapperManager->get($mapperClass);
    }

    /**
     * Get form
     *
     * @param string $formClass
     * @return FormInterface
     */
    public function getForm($formClass)
    {
        return $this->formManager->get($formClass);
    }

    /**
     * Get service from entity class
     *
     * @param string $entityClass
     * @return ServiceInterface
     */
    public function getServiceFromEntityClass($entityClass)
    {
        return $this->serviceManager->get($this->getServiceClassFromEntityClass($entityClass));
    }
    
    /**
     * Get mapper from entity class
     * 
     * @param string $entityClass
     * @return MapperInterface
     */
    public function getMapperFromEntityClass($entityClass)
    {
        return $this->mapperManager->get($this->getMapperClassFromEntityClass($entityClass));
    }

    /**
     * Get service class from entity class
     *
     * @param string $entityClass
     * @return string
     */
    public function getServiceClassFromEntityClass($entityClass)
    {
        return $this->entityToService[$entityClass];
    }
    
    /**
     * Get mapper class from entity class
     * 
     * @param string $entityClass
     * @return string
     */
    public function getMapperClassFromEntityClass($entityClass)
    {
        return $this->entityToMapper[$entityClass];
    }

    /**
     * Get service class from controller class
     *
     * @param string $controllerClass
     * @return string
     */
    public function getServiceClassFromControllerClass($controllerClass)
    {
        return $this->controllerToService[$controllerClass];
    }

    /**
     * Get controller options from controller class
     *
     * @param string $controllerClass
     * @return string
     */
    public function getControllerOptionsFromControllerClass($controllerClass)
    {
        return $this->controllerToOptions[$controllerClass];
    }

    /**
     * Get entity class from mapper class
     *
     * @param string $mapperClass
     * @return string
     */
    public function getEntityClassFromMapperClass($mapperClass)
    {
        return $this->mapperToEntity[$mapperClass];
    }
    
    /**
     * Get entity class from service class
     * 
     * @param string $serviceClass
     * @return string
     */
    public function getEntityClassFromServiceClass($serviceClass)
    {
        return $this->serviceToEntity[$serviceClass];
    }

    /**
     * Get mapper class from service class
     *
     * @param string $serviceClass
     * @return string
     */
    public function getMapperClassFromServiceClass($serviceClass)
    {
        return $this->serviceToMapper[$serviceClass];
    }

    /**
     * Get forms from service class
     *
     * @param string $serviceClass
     * @return string
     */
    public function getFormsFromServiceClass($serviceClass)
    {
        return $this->serviceToForms[$serviceClass];
    }
}
