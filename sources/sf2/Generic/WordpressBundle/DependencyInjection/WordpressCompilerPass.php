<?php

namespace Generic\WordpressBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class WordpressCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('wordpress.loader')) {
            return ;
        }

        $wordpress_loader_definition = $container->getDefinition('wordpress.loader');

        $shortcodesLists = array(
            $container->findTaggedServiceIds('wordpress.shortcodeRender'),
            $container->findTaggedServiceIds('wordpress.shortcodeGallery')
        );

        foreach ($shortcodesLists as $shortcodes) {
            foreach ($shortcodes as $id=>$attr) {
                $wordpress_loader_definition->addMethodCall('addShortcode', array(new Reference($id)));
            }
        }

        $metaboxes_service_ids = $container->findTaggedServiceIds('wordpress.metabox');

        foreach ($metaboxes_service_ids as $id=>$attr) {
            $wordpress_loader_definition->addMethodCall('addMetabox', array(new Reference($id)));
        }

        $menu_service_ids = $container->findTaggedServiceIds('wordpress.admin_menu');

        foreach ($menu_service_ids as $id=>$attr) {
            $wordpress_loader_definition->addMethodCall('addAdminMenu', array(new Reference($id)));
        }
    }
}
