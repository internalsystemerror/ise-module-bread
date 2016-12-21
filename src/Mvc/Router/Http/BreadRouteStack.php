<?php

namespace IseBread\Mvc\Router\Http;

use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Validator\Uuid;

class BreadRouteStack extends TreeRouteStack
{

    /**
     * Class constants
     */
    const IDENTIFIER    = 'id';
    const ACTION_INDEX  = 'browse';
    const ACTION_CREATE = 'add';
    const ACTIONS       = ['read', 'add', 'edit', 'delete', 'enable', 'disable'];

    /**
     * {@inheritDoc}
     */
    public static function factory($options = array())
    {
        $options['routes'] = self::parseRoutes($options['routes']);
        return parent::factory($options);
    }

    /**
     * {@inheritDoc}
     */
    protected function init()
    {
        parent::init();
        $this->routePluginManager->setInvokableClass('bread', __NAMESPACE__ . '\Bread');
    }

    /**
     * Check routes for bread type
     *
     * @param  array $routes Routes to parse
     * @return array
     */
    protected static function parseRoutes(array $routes)
    {
        // Begin parsed routes
        $parsedRoutes = [];
        foreach ($routes as $key => $route) {
            // Add bread routes
            $parsedRoutes[$key] = self::parseBreadRoutes($route);

            // Check for child routes
            $childRoutes = [];
            if (isset($route['child_routes'])) {
                $childRoutes = self::parseRoutes($route['child_routes']);
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
    protected static function parseBreadRoutes($route)
    {
        if (!isset($route['type']) || $route['type'] !== 'bread') {
            return $route;
        }
        
        // Set variables
        $identifier = self::IDENTIFIER;
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
            'options'      => ['defaults' => ['action' => self::ACTION_INDEX]],
            'child_routes' => self::createChildRoutes($identifier, $constraints),
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
    protected static function createChildRoutes($identifier, $constraints)
    {
        // Loop through actions
        $childRoutes = [];
        foreach (self::ACTIONS as $action) {
            if ($action === self::ACTION_CREATE) {
                // Handle add as special case
                continue;
            }
            // Add action
            $childRoutes[$action] = self::defaultConstrainedAction($action, $identifier, $constraints);
        }

        // Add special case add action
        $childRoutes[self::ACTION_CREATE] = self::defaultCreateAction();

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
    protected static function defaultConstrainedAction($action, $identifier, array $constraints)
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
    protected static function defaultCreateAction()
    {
        return [
            'type'    => 'literal',
            'options' => [
                'route'    => '/' . self::ACTION_CREATE,
                'defaults' => [
                    'action' => self::ACTION_CREATE,
                ]
            ],
        ];
    }
}
