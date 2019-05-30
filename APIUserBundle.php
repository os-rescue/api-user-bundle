<?php

namespace API\UserBundle;

use API\UserBundle\DependencyInjection\Compiler\CheckForMailerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class APIUserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new CheckForMailerPass());
    }
}
