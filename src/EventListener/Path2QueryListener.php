<?php

namespace Pmaxs\Path2queryBundle\EventListener;

use Pmaxs\Path2queryBundle\Router\Path2QueryRouter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class Path2QueryListener implements EventSubscriberInterface
{
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['resolveQuery', 0],
            ],
        ];
    }

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
}
