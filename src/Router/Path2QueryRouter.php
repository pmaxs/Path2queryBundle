<?php

namespace Pmaxs\Path2queryBundle\Router;

use Pmaxs\Path2queryBundle\Generator\Path2QueryUrlGenerator;

class Path2QueryRouter extends \Symfony\Bundle\FrameworkBundle\Routing\Router
{
    const QUERY_PARAM = '__path2query__';

    public function setOptions(array $options)
    {
        return parent::setOptions(array_merge(['generator_class' => Path2QueryUrlGenerator::class], $options));
    }
}
