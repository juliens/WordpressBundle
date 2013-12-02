<?php

namespace Generic\WordpressBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Generic\WordpressBundle\DependencyInjection\ControllerResolverPass;
use Generic\WordpressBundle\Security\WordpressAuthenticationFactory;
use Generic\WordpressBundle\DependencyInjection\WordpressCompilerPass;

class GenericWordpressBundle extends Bundle
{
    public function build(ContainerBuilder $container)                               
    {                                                                                
        parent::build($container);                                                   
                                                                                     
        $extension = $container->getExtension('security');                           
        $extension->addSecurityListenerFactory(new WordpressAuthenticationFactory()); 
        $container->addCompilerPass(new WordpressCompilerPass());
        //$container->addCompilerPass(new ControllerResolverPass());
    }   

}
