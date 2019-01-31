<?php

namespace Pmaxs\Path2queryBundle;

use Pmaxs\Path2queryBundle\DependencyInjection\Compiler\SetJMSI18nRouting;
use Pmaxs\Path2queryBundle\DependencyInjection\Compiler\SetRouterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PmaxsPath2queryBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SetJMSI18nRouting());
        $container->addCompilerPass(new SetRouterPass());
    }
}
