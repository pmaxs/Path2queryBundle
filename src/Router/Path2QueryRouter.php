<?php

namespace Pmaxs\Path2queryBundle\Router;

if (class_exists('\\JMS\\I18nRoutingBundle\\Router\\I18nRouter')) {
    class Path2QueryRouterTmp extends \JMS\I18nRoutingBundle\Router\I18nRouter
    {
        const ROUTER_ORIGIN = 'JMS';

        public function getPath2QueryRouteCollection()
        {
            return $this->getOriginalRouteCollection();
        }
    }

} else {
    class Path2QueryRouterTmp extends \Symfony\Bundle\FrameworkBundle\Routing\Router
    {
        const ROUTER_ORIGIN = 'SYMFONY';

        public function getPath2QueryRouteCollection()
        {
            return $this->getRouteCollection();
        }
    }
}

class Path2QueryRouter extends Path2QueryRouterTmp
{
    /**
     * Route path2query enabled param
     */
    const ENABLED_PARAM = '__path2query__';

    /**
     * Query param name
     */
    const QUERY_PARAM = '__path2query_param__';

    public function getPath2QueryRouteCollection()
    {
        return parent::getPath2QueryRouteCollection();
    }

    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        $routes = $this->getPath2QueryRouteCollection();

        if ('JMS' == self::ROUTER_ORIGIN) {
            $route = $routes->get($name);
        } else {
            $generator = $this->getGenerator();
            $locale = $parameters['_locale']
                ?? $generator->getContext()->getParameter('_locale')
                    ?: $this->defaultLocale;
            $route = null;

            if (null !== $locale) {
                do {
                    if (null !== ($route = $routes->get($name.'.'.$locale)) && $route->getDefault('_canonical_route') === $name) {
                        break;
                    }
                } while (false !== $locale = strstr($locale, '_', true));
            }

            if (empty($route)) {
                $route = $routes->get($name);
            }
        }

        if (empty($route) || empty($route->getDefault(self::ENABLED_PARAM)) || isset($parameters[self::QUERY_PARAM])) {
            return parent::generate($name, $parameters, $referenceType);
        }

        $routeVars = $route->compile()->getVariables();
        $queryVars = [];

        foreach ($parameters as $var => $val) {
            if (!in_array($var, $routeVars)) {
                $queryVars[$var] = $val;
                unset($parameters[$var]);
            }
        }

        $query = '';
        if (!empty($queryVars)) {
            foreach ($queryVars as $var => $val) {
                if (is_null($var) || !strlen($var) || is_null($val) || !strlen($val)) {
                    continue;
                }

                $query.= '/' . urlencode($var) . '/' . urlencode($val);
            }
        }

        $parameters[self::QUERY_PARAM] = ltrim($query, '/');

        return parent::generate($name, $parameters, $referenceType);
    }
}
