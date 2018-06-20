<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\ServiceManager;

use Ise\Bread\Mapper\MapperInterface;
use Ise\Bread\Options\BreadOptions;
use Ise\Bread\Options\ControllerOptions;
use Ise\Bread\Options\EntityOptions;
use Ise\Bread\Service\ServiceInterface;
use Zend\Form\FormInterface;

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
     * @var BreadOptions
     */
    protected $options;

    /**
     * @var string[]
     */
    protected $entityToService;

    /**
     * @var string[]
     */
    protected $entityToMapper;

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
     * @var array[]
     */
    protected $serviceToForms;

    /**
     * @var string[]
     */
    protected $controllerToService;

    /**
     * @var ControllerOptions[]
     */
    protected $controllerToOptions;

    /**
     * Constructor
     *
     * @param ServicePluginManager $serviceManager
     * @param MapperPluginManager  $mapperManager
     * @param FormPluginManager    $formManager
     * @param BreadOptions         $options
     */
    public function __construct(
        ServicePluginManager $serviceManager,
        MapperPluginManager $mapperManager,
        FormPluginManager $formManager,
        BreadOptions $options
    ) {
        // Set properties
        $this->serviceManager = $serviceManager;
        $this->mapperManager  = $mapperManager;
        $this->formManager    = $formManager;
        $this->options        = $options;

        // Create entity map
        foreach ($this->options->getAllEntityOptions() as $entityOptions) {
            $this->entityToService[$entityOptions->getClass()] = $entityOptions->getService();
            $this->entityToMapper[$entityOptions->getClass()]  = $entityOptions->getMapper();

            $this->serviceToEntity[$entityOptions->getService()] = $entityOptions->getClass();
            $this->serviceToMapper[$entityOptions->getService()] = $entityOptions->getMapper();

            $this->mapperToEntity[$entityOptions->getMapper()] = $entityOptions->getClass();
        }

        // Create controller map
        foreach ($this->options->getAllControllerOptions() as $controllerOptions) {
            $this->controllerToService[$controllerOptions->getClass()] =
                $this->entityToService[$controllerOptions->getEntityClass()];
            $this->controllerToOptions[$controllerOptions->getClass()] = $controllerOptions;
        }

        // Create service map
        foreach ($this->options->getAllServiceOptions() as $serviceOptions) {
            $this->serviceToForms[$serviceOptions->getClass()] = $serviceOptions->getForms();
        }
    }

    /**
     * Get entity
     *
     * @param string $entityClass
     *
     * @return EntityOptions|null
     */
    public function getEntityOptions(string $entityClass): ?EntityOptions
    {
        return $this->options->getEntityOptions($entityClass);
    }

    /**
     * Get controller base class
     *
     * @param string $requestedName
     *
     * @return string|null
     */
    public function getControllerBaseClass(string $requestedName): ?string
    {
        $options = $this->options->getControllerOptions($requestedName);
        if (!$options) {
            return null;
        }

        return $options->getBaseClass();
    }

    /**
     * Get service base class
     *
     * @param string $requestedName
     *
     * @return string|null
     */
    public function getServiceBaseClass(string $requestedName): ?string
    {
        $options = $this->options->getServiceOptions($requestedName);
        if (!$options) {
            return null;
        }

        return $options->getBaseClass();
    }

    /**
     * Get mapper base class
     *
     * @param string $requestedName
     *
     * @return string|null
     */
    public function getMapperBaseClass(string $requestedName): ?string
    {
        $options = $this->options->getMapperOptions($requestedName);
        if (!$options) {
            return null;
        }

        return $options->getBaseClass();
    }

    /**
     * Get service
     *
     * @param string $serviceClass
     *
     * @return ServiceInterface
     */
    public function getService(string $serviceClass): ServiceInterface
    {
        return $this->serviceManager->get($serviceClass);
    }

    /**
     * Get mapper
     *
     * @param string $mapperClass
     *
     * @return MapperInterface
     */
    public function getMapper(string $mapperClass): MapperInterface
    {
        return $this->mapperManager->get($mapperClass);
    }

    /**
     * Get form
     *
     * @param string $formClass
     *
     * @return FormInterface
     */
    public function getForm(string $formClass): FormInterface
    {
        return $this->formManager->get($formClass);
    }

    /**
     * Get service from entity class
     *
     * @param string $entityClass
     *
     * @return ServiceInterface
     */
    public function getServiceFromEntityClass(string $entityClass): ServiceInterface
    {
        return $this->serviceManager->get($this->getServiceClassFromEntityClass($entityClass));
    }

    /**
     * Get mapper from entity class
     *
     * @param string $entityClass
     *
     * @return MapperInterface
     */
    public function getMapperFromEntityClass(string $entityClass): MapperInterface
    {
        return $this->mapperManager->get($this->getMapperClassFromEntityClass($entityClass));
    }

    /**
     * Get service class from entity class
     *
     * @param string $entityClass
     *
     * @return string
     */
    public function getServiceClassFromEntityClass(string $entityClass): string
    {
        return $this->entityToService[$entityClass];
    }

    /**
     * Get mapper class from entity class
     *
     * @param string $entityClass
     *
     * @return string
     */
    public function getMapperClassFromEntityClass(string $entityClass): string
    {
        return $this->entityToMapper[$entityClass];
    }

    /**
     * Get service class from controller class
     *
     * @param string $controllerClass
     *
     * @return string
     */
    public function getServiceClassFromControllerClass(string $controllerClass): string
    {
        return $this->controllerToService[$controllerClass];
    }

    /**
     * Get controller options from controller class
     *
     * @param string $controllerClass
     *
     * @return ControllerOptions
     */
    public function getControllerOptionsFromControllerClass(string $controllerClass): ControllerOptions
    {
        return $this->controllerToOptions[$controllerClass];
    }

    /**
     * Get entity class from mapper class
     *
     * @param string $mapperClass
     *
     * @return string
     */
    public function getEntityClassFromMapperClass(string $mapperClass): string
    {
        return $this->mapperToEntity[$mapperClass];
    }

    /**
     * Get entity class from service class
     *
     * @param string $serviceClass
     *
     * @return string
     */
    public function getEntityClassFromServiceClass(string $serviceClass): string
    {
        return $this->serviceToEntity[$serviceClass];
    }

    /**
     * Get mapper class from service class
     *
     * @param string $serviceClass
     *
     * @return string
     */
    public function getMapperClassFromServiceClass(string $serviceClass): string
    {
        return $this->serviceToMapper[$serviceClass];
    }

    /**
     * Get forms from service class
     *
     * @param string $serviceClass
     *
     * @return array
     */
    public function getFormsFromServiceClass(string $serviceClass): array
    {
        return $this->serviceToForms[$serviceClass];
    }
}
