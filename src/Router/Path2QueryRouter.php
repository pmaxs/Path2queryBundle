<?php

namespace Pmaxs\Path2queryBundle\Router;

if (class_exists('\\JMS\\I18nRoutingBundle\\Router\\I18nRouter')) {
    class Path2QueryRouterTmp extends \JMS\I18nRoutingBundle\Router\I18nRouter
    {
        protected function getPath2QueryRouteCollection()
        {
            return $this->getOriginalRouteCollection();
        }
    }

} else {
    class Path2QueryRouterTmp extends \Symfony\Bundle\FrameworkBundle\Routing\Router
    {
        protected function getPath2QueryRouteCollection()
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

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        $route = $this->getPath2QueryRouteCollection()->get($name);

        if (is_null($route->getDefault(self::ENABLED_PARAM)) || isset($parameters[self::QUERY_PARAM])) {
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

        $parameters[self::QUERY_PARAM] = $query;

        return parent::generate($name, $parameters, $referenceType);
    }
}
