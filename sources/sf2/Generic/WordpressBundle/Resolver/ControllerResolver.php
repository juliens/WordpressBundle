<?php

namespace Generic\WordpressBundle\Resolver;

use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ControllerResolver implements ControllerResolverInterface
{
    private $defaultResolver;
    private $container;

    public function get($id)
    {
        return $this->container->get($id);
    }

    public function __construct(BaseControllerResolver $resolver, $container)
    {
        $this->defaultResolver = $resolver;
        $this->container = $container;
    }

    public function getController(Request $request)
    {
        var_dump($request->attributes->get('_controller'));
        if ($request->attributes->has('_controller')) {
            return $this->defaultResolver->getController($request);
        }
        var_dump('test');
        $call =  $this->defaultResolver->getController($request);
        return function () use ($call) { return call_user_func_array($call, func_get_args()); };
        $callback = $this->defaultResolver->getController($request);
        $wordpress_loader = $this->get('wordpress.loader');
        $test = $this;
        return function () use ($wordpress_loader, $callback, $request, $test) {
            if (is_callable($callback)) {
                try {
                    return $callback();
                } catch(\Exception $e) {
                    $wordpress_loader->load();
                    $post_id = url_to_postid('index.php'.$request->getPathInfo());
                    if ($post_id==null) {
                        throw $e;
                    }
                    $request->attributes->set('_controller', 'GenericWordpressBundle:Default:wordpress');
                    $request->attributes->set('_post_id', $post_id);
                    $callback = $test->defaultResolver->getController($request);
                    $args = $test->defaultResolver->getArguments($request, $callback);
                    return call_user_func_array($callback, $args);
                    $toReturn = $controller_resolver->getWordpressController($request, $e);
                }
            }

        };
    }

    public function getWordpressController(Request $request, $e)
    {
        var_dump('test');
        return false;
        var_dump('test');
        $this->get('wordpress.loader')->load();
        $post_id = url_to_postid('index.php'.$request->getPathInfo());
        if ($post_id==null) {
            throw $e;
        } else {
            $request->attributes->set('_controller', 'GenericWordpressBundle:Default:wordpress');
            $request->attributes->set('_post_id', $post_id);
            $callback = $this->defaultResolver->getController($request); 
            return $callback();
        }
        
    }

    public function getArguments(Request $request, $controller)
    {
        return $this->defaultResolver->getArguments($request, $controller);
    }
}
