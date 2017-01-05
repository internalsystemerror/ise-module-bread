<?php

namespace Ise\Bread\Listener;

use Ise\Bread\Router\Http\Bread;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\ModuleManager\ModuleEvent;
use Zend\Validator\Uuid;

class ConfigListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

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
     * On merge config event
     * 
     * @param ModuleEvent $event
     */
    public function onMergeConfig(ModuleEvent $event)
    {
        $configListener = $event->getConfigListener();
        $config         = $configListener->getMergedConfig(false);
        
        $config['router']['routes'] = $this->parseRoutes($config['router']['routes']);
        $configListener->setMergedConfig($config);
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

        // Set variables
        $identifier = Bread::IDENTIFIER;
        if (isset($route['options']['identifier'])) {
            $identifier = $route['options']['identifier'];
        }

        $uuidRegex   = trim(Uuid::REGEX_UUID, '/^$');
        $constraints = [$identifier => $uuidRegex];
        if (isset($route['options']['constraints'])) {
            $constraints = $route['options']['constraints'];
        }

        // Create options
        $options = array_merge_recursive($route, [
            'options'      => ['defaults' => ['action' => Bread::ACTION_INDEX]],
            'child_routes' => $this->createChildRoutes($identifier, $constraints),
        ]);
        if (!isset($options['may_terminate'])) {
            $options['may_terminate'] = true;
        }
        $options['type'] = 'literal';

        return $options;
    }

    /**
     * Create default routes
     *
     * @param  string $identifier  The default identifier
     * @param  array  $constraints Constraints for the identifier
     * @return array
     */
    protected function createChildRoutes($identifier, $constraints)
    {
        // Loop through actions
        $childRoutes = [];
        foreach (Bread::ACTIONS as $action) {
            if ($action === Bread::ACTION_CREATE) {
                // Handle add as special case
                continue;
            }
            // Add action
            $childRoutes[$action] = $this->defaultConstrainedAction($action, $identifier, $constraints);
        }

        // Add special case add action
        $childRoutes[Bread::ACTION_CREATE] = $this->defaultCreateAction();

        return $childRoutes;
    }

    /**
     * Create default constrained action
     *
     * @param  string $action      Action this is for
     * @param  string $identifier  The default identifier
     * @param  array  $constraints Constraints for the identifier
     * @return array
     */
    protected function defaultConstrainedAction($action, $identifier, array $constraints)
    {
        return [
            'type'    => 'segment',
            'options' => [
                'route'       => '/' . $action . '/:' . $identifier,
                'constraints' => $constraints,
                'defaults'    => [
                    'action' => $action,
                ]
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
                'route'    => '/' . Bread::ACTION_CREATE,
                'defaults' => [
                    'action' => Bread::ACTION_CREATE,
                ]
            ],
        ];
    }
}
