<?php

namespace Ise\Bread\Listener;

use Ise\Bread\EventManager\BreadEvent;
use Ise\Bread\Exception\InvalidArgumentException;
use Ise\Bread\Options\AbstractClassOptions;
use Ise\Bread\Options\BreadOptions;
use Ise\Bread\Options\ControllerOptions;
use Ise\Bread\Options\EntityOptions;
use Ise\Bread\Options\MapperOptions;
use Ise\Bread\Options\ServiceOptions;
use ReflectionClass;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Filter\Word\CamelCaseToSeparator;
use Zend\ModuleManager\ModuleEvent;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\Uuid;

class ConfigListener implements ListenerAggregateInterface
{

    use ListenerAggregateTrait;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $breadConfig;

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            ModuleEvent::EVENT_MERGE_CONFIG,
            [$this, 'onMergeConfig'],
            $priority
        );
    }

    /**
     * On merge configuration event
     *
     * @param ModuleEvent $event
     */
    public function onMergeConfig(ModuleEvent $event)
    {
        // Get current config
        $configListener    = $event->getConfigListener();
        $this->config      = $configListener->getMergedConfig(false);
        $this->breadConfig = new BreadOptions($this->config['ise']['bread']);

        // Loop through entities
        foreach ($this->breadConfig->getEntities() as $entity) {
            $this->addEntityConfig($entity);
        }
        
        // Loop through controllers
        foreach ($this->breadConfig->getControllers() as $controller) {
            $this->addControllerConfig($controller);
        }
        
        // Loop through services
        foreach ($this->breadConfig->getServices() as $service) {
            $this->addServiceConfig($service);
        }
        
        // Loop through mappers
        foreach ($this->breadConfig->getMappers() as $mapper) {
            $this->addMapperConfig($mapper);
        }

        // Parse routes
        $this->config['router']['routes'] = $this->parseRoutes($this->config['router']['routes']);

        // Set new config
        $this->config['ise']['bread'] = $this->breadConfig->toArray();
        $configListener->setMergedConfig($this->config);
    }

    /**
     * Add entity configuration
     *
     * @param EntityOptions $options
     * @throws InvalidArgumentException
     */
    protected function addEntityConfig(EntityOptions $options)
    {
        // Check class
        if (!$options->getClass()) {
            throw new InvalidArgumentException('EntityOptions must have the entity class set.');
        }
        
        // Get entity details
        $reflection = new ReflectionClass($options->getClass());
        $namespace  = $reflection->getNamespaceName();
        $baseName   = substr($namespace, 0, strrpos($namespace, '\\'));
        $entityName = $reflection->getShortName();
        
        // Check alias
        if (!$options->getAlias()) {
            $options->setAlias($entityName);
        }
        
        // Setup config
        $this->setupController($options, $baseName, $entityName);
        $this->setupService($options, $baseName, $entityName);
        $this->setupMapper($options, $baseName, $entityName);
    }
    
    /**
     * Add controller configuration
     *
     * @param ControllerOptions $options
     * @throws InvalidArgumentException
     */
    protected function addControllerConfig(ControllerOptions $options)
    {
        // Check class
        if (!$options->getClass()) {
            throw new InvalidArgumentException('ControllerOptions must have the controller class set.');
        }
        
        // Check alias
        if (!$options->getAlias()) {
            $options->setAlias(preg_replace('/Controller$/', '', $options->getClass()));
        }
        
        // Save entity title
        $entityName = $this->breadConfig->getEntity($options->getEntityClass())->getAlias();
        if (!$options->getEntityTitle()) {
            $camelFilter            = new CamelCaseToSeparator;
            $options->setEntityTitle(strtolower($camelFilter->filter($entityName)));
        }
        
        // Add controllers
        if (!isset($this->config['controllers']['aliases'][$options->getAlias()])) {
            $this->config['controllers']['aliases'][$options->getAlias()] = $options->getClass();
        }
        if (!isset($this->config['controllers']['factories'][$options->getClass()])) {
            $this->config['controllers']['factories'][$options->getClass()] = $options->getFactory();
        }
    }
    
    protected function addServiceConfig(ServiceOptions $options)
    {
        // Setup manager
        $this->breadConfig->setServiceManager(
            $this->setManagerOptions(
                $this->breadConfig->getServiceManager(),
                $options
            )
        );
    }
    
    protected function addMapperConfig(MapperOptions $options)
    {
        // Setup manager
        $this->breadConfig->setMapperManager(
            $this->setManagerOptions(
                $this->breadConfig->getMapperManager(),
                $options
            )
        );
    }
    
    /**
     * Setup controller configuration for entity
     *
     * @param EntityOptions $options
     * @param type $namespace
     * @param type $entityName
     */
    protected function setupController(EntityOptions $options, $namespace, $entityName)
    {
        // Get controller options, return if null/false
        $controller = $options->getController();
        if (is_string($controller)) {
            $controller = ['class' => $controller];
        }
        if (!$controller) {
            return;
        }
                
        // Create alias
        if (!isset($controller['alias']) || !$controller['alias']) {
            if (!isset($controller['class']) || !$controller['class']) {
                $controller['alias'] = $namespace . '\Controller\\' . $entityName;
            } else {
                $controller['alias'] = preg_replace('/Controller$/', '', $controller['class']);
            }
        }
        
        // Create class
        if (!isset($controller['class']) || !$controller['class']) {
            $controller['class'] = $controller['alias'] . 'Controller';
        }
        
        // Add entity class
        if (!isset($controller['entityClass'])) {
            $controller['entityClass'] = $options->getClass();
        }
        
        // Save
        $options->setController($controller['class']);
        $this->breadConfig->setController($controller['class'], $controller);
    }

    /**
     * Setup service configuration for entity
     *
     * @param EntityOptions $options
     * @param string $namespace
     * @param string $entityName
     */
    public function setupService(EntityOptions $options, $namespace, $entityName)
    {
        // Get mapper options
        $service = $options->getService();
        if (is_string($service)) {
            $service = ['class' => $service];
        }
        
        // Create alias
        if (!isset($service['alias']) || !$service['alias']) {
            if (!isset($service['class']) || !$service['class']) {
                $service['alias'] = $namespace . '\Service\\' . $entityName;
            } else {
                $service['alias'] = preg_replace('/Service$/', '', $service['class']);
            }
        }
        
        // Create class
        if (!isset($service['class']) || !$service['class']) {
            $service['class'] = $service['alias'] . 'Service';
        }
        
        // Save
        $options->setService($service['class']);
        $this->breadConfig->setService($service['class'], $service);
        
        // Setup forms
        $this->setupForms(
            $this->breadConfig->getService($service['class']),
            $namespace,
            $entityName
        );
    }

    /**
     * Setup mapper configuration for entity
     *
     * @param EntityOptions $options
     * @param string $namespace
     * @param string $entityName
     */
    protected function setupMapper(EntityOptions $options, $namespace, $entityName)
    {
        // Get mapper options
        $mapper = $options->getMapper();
        if (is_string($mapper)) {
            $mapper = ['class' => $mapper];
        }
        
        // Create alias
        if (!isset($mapper['alias']) || !$mapper['alias']) {
            if (!isset($mapper['class']) || !$mapper['class']) {
                $mapper['alias'] = $namespace . '\Mapper\\' . $entityName;
            } else {
                $mapper['alias'] = preg_replace('/Mapper/', '', $mapper['class']);
            }
            $mapper['alias'] = $namespace . '\Mapper\\' . $entityName;
        }
        
        // Create class
        if (!isset($mapper['class']) || !$mapper['class']) {
            $mapper['class'] = $mapper['alias'] . 'Mapper';
        }
        
        // Save
        $options->setMapper($mapper['class']);
        $this->breadConfig->setMapper($mapper['class'], $mapper);
    }
    
    /**
     * Set manager options from options
     *
     * @param array $manager
     * @param AbstractClassOptions $options
     */
    protected function setManagerOptions(array $manager, AbstractClassOptions $options)
    {
        // Add alias
        if (!isset($manager['aliases'][$options->getAlias()])) {
            $manager['aliases'][$options->getAlias()] = $options->getClass();
        }
        
        // Add factory
        if (!isset($manager['factories'][$options->getClass()])) {
            $manager['factories'][$options->getClass()] = $options->getFactory();
        }
        
        return $manager;
    }

    /**
     * Setup forms configuration
     *
     * @param ServiceOptions $options
     * @param string $namespace
     * @param string $entityName
     */
    protected function setupForms(ServiceOptions $options, $namespace, $entityName)
    {
        // Get forms and namespace
        $forms         = $options->getForms();
        $formNamespace = $namespace . '\Form\\' . $entityName . '\\';
        
        // Loop through Bread available forms
        foreach (BreadEvent::getAvailableForms() as $action) {
            if (!isset($forms[$action]) || !$forms[$action]) {
                $forms[$action] = $formNamespace . ucfirst($action);
            }
        }
        
        $options->setForms($forms);
    }

    /**
     * Check routes for bread type
     *
     * @param  array $routes Routes to parse
     * @return array
     */
    protected function parseRoutes(array $routes)
    {
        // Begin parsed routes
        $parsedRoutes = [];
        foreach ($routes as $key => $route) {
            // Add bread routes
            $parsedRoutes[$key] = $this->parseBreadRoutes($route);

            // Check for child routes
            $childRoutes = [];
            if (isset($route['child_routes'])) {
                $childRoutes = $this->parseRoutes($route['child_routes']);
            }
            if (isset($parsedRoutes[$key]['child_routes'])) {
                $parsedRoutes[$key]['child_routes'] = array_merge($parsedRoutes[$key]['child_routes'], $childRoutes);
            }
        }
        return $parsedRoutes;
    }

    /**
     * Parse bread routes
     *
     * @param  array $route  Route to add bread routes to
     * @return array
     */
    protected function parseBreadRoutes($route)
    {
        if (!isset($route['type']) || $route['type'] !== 'bread') {
            return $route;
        }

        // Create options
        $defaults = [
            'options'      => ['defaults' => ['action' => BreadEvent::ACTION_INDEX]],
            'child_routes' => $this->createChildRoutes(),
        ];
        if (isset($route['options']['entity'])) {
            $entityOptions = $this->breadConfig->getEntity($route['options']['entity']);
            if (!$entityOptions) {
                throw new InvalidArgumentException(sprintf(
                    'Unable to find the entity: %s',
                    $route['options']['entity']
                ));
            }
            if (!isset($route['options']['defaults']['controller'])) {
                $controllerOptions = $this->breadConfig->getController($entityOptions->getController());
                if (!$controllerOptions) {
                    throw new InvalidArgumentException(sprintf(
                        'Unable to find a controller for the entity: %s',
                        $route['options']['entity']
                    ));
                }
                $defaults['options']['defaults']['controller'] = $controllerOptions->getAlias();
            }
            unset($route['options']['entity']);
        }
        
        $options = ArrayUtils::merge($defaults, $route);
        if (!isset($options['may_terminate'])) {
            $options['may_terminate'] = true;
        }
        $options['type'] = 'literal';

        return $options;
    }

    /**
     * Create default routes
     *
     * @return array
     */
    protected function createChildRoutes()
    {
        // Loop through actions
        $uuidRegex   = trim(Uuid::REGEX_UUID, '/^$');
        $childRoutes = [];
        foreach (BreadEvent::getAvailableActions() as $action) {
            if ($action === BreadEvent::ACTION_CREATE) {
                // Handle add as special case
                continue;
            }
            // Add action
            $childRoutes[$action] = $this->defaultConstrainedAction($action, $uuidRegex);
        }

        // Add special case add action
        $childRoutes[BreadEvent::ACTION_CREATE] = $this->defaultCreateAction();

        return $childRoutes;
    }

    /**
     * Create default constrained action
     *
     * @param  string $action    Action this is for
     * @param  string $uuidRegex UUID regex
     * @return array
     */
    protected function defaultConstrainedAction($action, $uuidRegex)
    {
        return [
            'type'    => 'segment',
            'options' => [
                'route'       => '/:' . BreadEvent::IDENTIFIER . '/' . $action,
                'constraints' => [BreadEvent::IDENTIFIER => $uuidRegex],
                'defaults'    => [
                    'action' => $action,
                ],
            ],
        ];
    }

    /**
     * Create default create action
     *
     * @return array
     */
    protected function defaultCreateAction()
    {
        return [
            'type'    => 'literal',
            'options' => [
                'route'    => '/' . BreadEvent::ACTION_CREATE,
                'defaults' => [
                    'action' => BreadEvent::ACTION_CREATE,
                ]
            ],
        ];
    }
}
