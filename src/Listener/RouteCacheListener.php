<?php

namespace Ise\Bread\Listener;

use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Literal;

class RouteCacheListener implements ListenerAggregateInterface
{

    const CACHE_SERVICE   = 'Ise\Cache\Route';
    const ROUTE_CACHEABLE = 'route-cacheable';
    const ROUTE_CACHED    = 'route-cached';

    /**
     * @var array
     */
    protected $listeners = [];
    
    /**
     * @var AbstractAdapter
     */
    protected $cacheService;
    
    /**
     * Constructor
     *
     * @param AbstractAdapter $cacheService
     */
    public function __construct(AbstractAdapter $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $eventManager)
    {
        $this->listeners[] = $eventManager->attach(
            MvcEvent::EVENT_ROUTE,
            [$this, 'routeLoad'],
            1000
        );
        $this->listeners[] = $eventManager->attach(
            MvcEvent::EVENT_ROUTE,
            [$this, 'routeSave'],
            -1000
        );
    }

    /**
     * {@inheritDoc}
     */
    public function detach(EventManagerInterface $eventManager)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($eventManager->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Load the route from cache if available
     *
     * @param MvcEvent $event
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function routeLoad(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof Request || $request->getMethod() !== Request::METHOD_GET || $request->getQuery()->count() > 0) {
            return;
        }

        $event->setParam(self::ROUTE_CACHEABLE, true);

        // Check if data is in cache
        $path       = $request->getUri()->getPath();
        $cacheKey   = $this->getCacheKey($path);
        $cachedData = $this->cacheService->getItem($cacheKey);

        if (!empty($cachedData)) {
            $event->setParam(self::ROUTE_CACHED, true);

            $cachedRoute = Literal::factory($cachedData);
            $router      = $event->getRouter();
            $router->addRoute($cachedData['name'], $cachedRoute, 1000);
        }
    }

    /**
     * Save the route into cache
     *
     * @param MvcEvent $event
     */
    public function routeSave(MvcEvent $event)
    {
        $match = $event->getRouteMatch();
        if (!$match || $event->getParam(self::ROUTE_CACHED) || !$event->getParam(self::ROUTE_CACHEABLE)) {
            return;
        }

        // Save data in cache
        $path     = $event->getRequest()->getUri()->getPath();
        $cacheKey = $this->getCacheKey($path);
        $data     = [
            'name'     => $event->getRouteMatch()->getMatchedRouteName(),
            'route'    => $path,
            'defaults' => $event->getRouteMatch()->getParams(),
        ];
        $this->cacheService->setItem($cacheKey, $data);
    }

    /**
     * Create ZF2 compatible cache key
     *
     * @param string $path
     * @return string
     */
    public function getCacheKey($path)
    {
        return 'route-' . md5($path);
    }
}
