<?php

namespace Pmaxs\Path2queryBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\ExpressionLanguage\Expression;

class SetJMSI18nRouting implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('jms_i18n_routing.router')) {
            return;
        }

        $container->findDefinition('pmaxs_path2query.router')
            ->setParent('jms_i18n_routing.router');

        $container->findDefinition('pmaxs_path2query.listener.path2query')
            ->replaceArgument(0, new Expression("service('router').getOriginalRouteCollection()"));
    }
}
