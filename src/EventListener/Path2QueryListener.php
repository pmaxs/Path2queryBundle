<?php
namespace Pmaxs\Path2queryBundle\EventListener;

use Pmaxs\Path2queryBundle\Router\Path2QueryRouter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * Listener to resolve locale from path or host
 */
class Path2QueryListener implements EventSubscriberInterface
{
    /**
     * Route collection
     * @var RouterInterface
     */
    protected $router;

    /**
     * Constructor
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
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
     * @param RequestEvent $event
     */
    public function setupRoutes(RequestEvent $event)
    {
        if ($event->getRequestType() != HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        foreach ($this->router->getPath2QueryRouteCollection() as $routeName => $route) {
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
     * @param RequestEvent $event
     */
    public function resolveQuery(RequestEvent $event)
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

    /**
     * Resolves query from path
     * @param RequestEvent $event
     */
    public function resolveQuery1(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (($query = $request->get(Path2QueryRouter::QUERY_PARAM))) {
            $query = explode('/', trim($query, '/'));

            foreach ($query as $queryPart) {
                $queryPart = urldecode($queryPart);
                $queryPart = explode('-', $queryPart, 2);
                $param = isset($queryPart[0]) ? trim($queryPart[0]) : null;
                $value = isset($queryPart[1]) ? trim($queryPart[1]) : null;

                if (isset($param) && strlen($param) && !$request->query->has($param)) {
                    $_REQUEST[$param] = $value;
                    $_GET[$param] = $value;
                    $request->query->set($param, $value);
                }
            }
        }
    }
}
