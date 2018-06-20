<?php
/**
 * @copyright 2018 Internalsystemerror Limited
 */
declare(strict_types=1);

namespace Ise\Bread\Listener;

use Ise\Bread\EventManager\BreadEvent;
use Ise\Bread\Exception\InvalidArgumentException;
use Ise\Bread\Options\AbstractFactoryClassOptions;
use Ise\Bread\Options\BreadOptions;
use Ise\Bread\Options\ControllerOptions;
use Ise\Bread\Options\EntityOptions;
use Ise\Bread\Options\MapperOptions;
use Ise\Bread\Options\ServiceOptions;
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
     * @var BreadOptions
     */
    protected $breadConfig;

    /**
     * @inheritdoc
     */
    public function attach(EventManagerInterface $events, $priority = 1): void
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
     *
     * @return void
     */
    public function onMergeConfig(ModuleEvent $event): void
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
     * Setup service configuration for entity
     *
     * @param EntityOptions $options
     * @param string        $namespace
     * @param string        $entityName
     *
     * @return void
     */
    public function setupService(EntityOptions $options, string $namespace, string $entityName): void
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
     * Add entity configuration
     *
     * @param EntityOptions $options
     *
     * @throws InvalidArgumentException
     * @throws \ReflectionException
     *
     * @return void
     */
    protected function addEntityConfig(EntityOptions $options): void
    {
        // Check class
        if (!$options->getClass()) {
            throw new InvalidArgumentException('EntityOptions must have the entity class set.');
        }

        // Get entity details
        $reflection = new \ReflectionClass($options->getClass());
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
     *
     * @return void
     * @throws InvalidArgumentException
     */
    protected function addControllerConfig(ControllerOptions $options): void
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
            $camelFilter = new CamelCaseToSeparator;
            $options->setEntityTitle(strtolower($camelFilter->filter($entityName)));
        }

        // Add controllers
        if (!$this->config['controllers']['aliases'][$options->getAlias()]) {
            $this->config['controllers']['aliases'][$options->getAlias()] = $options->getClass();
        }
        if (!$this->config['controllers']['factories'][$options->getClass()]) {
            $this->config['controllers']['factories'][$options->getClass()] = $options->getFactory();
        }
    }

    /**
     * Add service config
     *
     * @param ServiceOptions $options
     *
     * @return void
     */
    protected function addServiceConfig(ServiceOptions $options): void
    {
        // Setup manager
        $this->breadConfig->setServiceManager(
            $this->setManagerOptions(
                $this->breadConfig->getServiceManager(),
                $options
            )
        );
    }

    /**
     * Add mapper config
     *
     * @param MapperOptions $options
     *
     * @return void
     */
    protected function addMapperConfig(MapperOptions $options): void
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
     * @param string        $namespace
     * @param string        $entityName
     *
     * @return void
     */
    protected function setupController(EntityOptions $options, string $namespace, string $entityName): void
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
        if (!$controller['alias'] || !$controller['alias']) {
            if (!$controller['class'] || !$controller['class']) {
                $controller['alias'] = $namespace . '\Controller\\' . $entityName;
            } else {
                $controller['alias'] = preg_replace('/Controller$/', '', $controller['class']);
            }
        }

        // Create class
        if (!$controller['class'] || !$controller['class']) {
            $controller['class'] = $controller['alias'] . 'Controller';
        }

        // Add entity class
        if (!$controller['entityClass']) {
            $controller['entityClass'] = $options->getClass();
        }

        // Save
        $options->setController($controller['class']);
        $this->breadConfig->setController($controller['class'], $controller);
    }

    /**
     * Setup mapper configuration for entity
     *
     * @param EntityOptions $options
     * @param string        $namespace
     * @param string        $entityName
     *
     * @return void
     */
    protected function setupMapper(EntityOptions $options, $namespace, $entityName): void
    {
        // Get mapper options
        $mapper = $options->getMapper();
        if (is_string($mapper)) {
            $mapper = ['class' => $mapper];
        }

        // Create alias
        if (!$mapper['alias'] || !$mapper['alias']) {
            if (!$mapper['class'] || !$mapper['class']) {
                $mapper['alias'] = $namespace . '\Mapper\\' . $entityName;
            } else {
                $mapper['alias'] = preg_replace('/Mapper/', '', $mapper['class']);
            }
            $mapper['alias'] = $namespace . '\Mapper\\' . $entityName;
        }

        // Create class
        if (!$mapper['class'] || !$mapper['class']) {
            $mapper['class'] = $mapper['alias'] . 'Mapper';
        }

        // Save
        $options->setMapper($mapper['class']);
        $this->breadConfig->setMapper($mapper['class'], $mapper);
    }

    /**
     * Set manager options from options
     *
     * @param array                       $manager
     * @param AbstractFactoryClassOptions $options
     *
     * @return array
     */
    protected function setManagerOptions(array $manager, AbstractFactoryClassOptions $options): array
    {
        // Add alias
        if (!$manager['aliases'][$options->getAlias()]) {
            $manager['aliases'][$options->getAlias()] = $options->getClass();
        }

        // Add factory
        if (!$manager['factories'][$options->getClass()]) {
            $manager['factories'][$options->getClass()] = $options->getFactory();
        }

        return $manager;
    }

    /**
     * Setup forms configuration
     *
     * @param ServiceOptions $options
     * @param string         $namespace
     * @param string         $entityName
     *
     * @return void
     */
    protected function setupForms(ServiceOptions $options, string $namespace, string $entityName): void
    {
        // Get forms and namespace
        $forms         = $options->getForms();
        $formNamespace = $namespace . '\Form\\' . $entityName . '\\';

        // Loop through Bread available forms
        foreach (BreadEvent::getAvailableForms() as $action) {
            if (!$forms[$action] || !$forms[$action]) {
                $forms[$action] = $formNamespace . ucfirst($action);
            }
        }

        $options->setForms($forms);
    }

    /**
     * Check routes for bread type
     *
     * @param  array $routes Routes to parse
     *
     * @return array
     */
    protected function parseRoutes(array $routes): array
    {
        // Begin parsed routes
        $parsedRoutes = [];
        foreach ($routes as $key => $route) {
            // Add bread routes
            $parsedRoutes[$key] = $this->parseBreadRoutes($route);

            // Check for child routes
            $childRoutes = [];
            if ($route['child_routes']) {
                $childRoutes = $this->parseRoutes($route['child_routes']);
            }
            if ($parsedRoutes[$key]['child_routes']) {
                $parsedRoutes[$key]['child_routes'] = array_merge($parsedRoutes[$key]['child_routes'], $childRoutes);
            }
        }
        return $parsedRoutes;
    }

    /**
     * Parse bread routes
     *
     * @param  array $route Route to add bread routes to
     *
     * @return array
     */
    protected function parseBreadRoutes($route): array
    {
        if (!$route['type'] || $route['type'] !== 'bread') {
            return $route;
        }

        // Create options
        $defaults = [
            'options'      => ['defaults' => ['action' => BreadEvent::ACTION_INDEX]],
            'child_routes' => $this->createChildRoutes(),
        ];
        if ($route['options']['entity']) {
            $entityOptions = $this->breadConfig->getEntity($route['options']['entity']);
            if (!$entityOptions) {
                throw new InvalidArgumentException(sprintf(
                    'Unable to find the entity: %s',
                    $route['options']['entity']
                ));
            }
            if (!$route['options']['defaults']['controller']) {
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
        if (!$options['may_terminate']) {
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
    protected function createChildRoutes(): array
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
     *
     * @return array
     */
    protected function defaultConstrainedAction(string $action, string $uuidRegex): array
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
    protected function defaultCreateAction(): array
    {
        return [
            'type'    => 'literal',
            'options' => [
                'route'    => '/' . BreadEvent::ACTION_CREATE,
                'defaults' => [
                    'action' => BreadEvent::ACTION_CREATE,
                ],
            ],
        ];
    }
}
