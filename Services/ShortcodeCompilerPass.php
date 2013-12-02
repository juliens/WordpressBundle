<?php

namespace Generic\WordpressBundle\Services;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ShortcodeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('wordpress.loader')) {
            return ;
        }

        $wordpress_loader_definition = $container->getDefinition('wordpress.loader');

        $shortcodes_service_ids = $container->findTaggedServiceIds('wordpress.shortcode');

        foreach ($shortcodes_service_ids as $id=>$attr) {
            $wordpress_loader_definition->addMethodCall('addShortcode', array(new Reference($id)));
        }
    }
}
