<?php

namespace Pmaxs\Path2queryBundle\DependencyInjection\Compiler;

use Pmaxs\Path2queryBundle\Generator\Path2QueryUrlGenerator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SetRouterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $routerDefinition = $container->getDefinition('router.default');
        $routerDefinition->setArgument(2, array_merge($routerDefinition->getArgument(2), [
            'generator_class' => Path2QueryUrlGenerator::class,
        ]));

        $container->setAlias('router', 'pmaxs_path2query.router');
    }
}
