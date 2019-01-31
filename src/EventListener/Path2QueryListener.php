<?php
namespace Pmaxs\Path2queryBundle\EventListener;

use Pmaxs\Path2queryBundle\Router\Path2QueryRouter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listener to resolve locale from path or host
 */
class Path2QueryListener implements EventSubscriberInterface
{
    /**
     * Route collection
     * @var RouteCollection
     */
    protected $routes;

    /**
     * Constructor
     * @param RouteCollection $routes
     */
    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(
                array('setupRoutes', 33),
                array('resolveQuery', 0),
            ),
        );
    }

    /**
     * Setups routes, adds query parameter
     * @param GetResponseEvent $event
     */
    public function setupRoutes(GetResponseEvent $event)
    {
        if ($event->getRequestType() != HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        foreach ($this->routes as $routeName => $route) {
            if (!$route->getDefault(Path2QueryRouter::ENABLED_PARAM)) {
                continue;
            }

            $route
                ->setPath(rtrim($route->getPath(), '/') . '/{' . Path2QueryRouter::QUERY_PARAM . '}')
                ->setRequirement(Path2QueryRouter::QUERY_PARAM, '.*')
                ->setDefault(Path2QueryRouter::QUERY_PARAM, '')
            ;
        }
    }

    /**
     * Resolves query from path
     * @param GetResponseEvent $event
     */
    public function resolveQuery(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (($query = $request->get(Path2QueryRouter::QUERY_PARAM))) {
            $query = explode('/', trim($query, '/'));

            for ($i = 0; $i < count($query) - 1; $i += 2) {
                $param = isset($query[$i]) ? urldecode($query[$i]) : null;
                $value = isset($query[$i + 1]) ? urldecode($query[$i + 1]) : null;

                if (!$request->query->has($param)) {
                    $_REQUEST[$param] = $value;
                    $_GET[$param] = $value;
                    $request->query->set($param, $value);
                }
            }
        }
    }
}
