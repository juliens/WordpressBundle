<?php

namespace Generic\WordpressBundle\DependencyInjection;

use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpFoundation\Request;

class ControllerResolver implements ControllerResolverInterface
{
    private $defaultResolver;

    public function __construct(BaseControllerResolver $resolver)
    {
        $this->defaultResolver = $resolver;
    }

    public function getController(Request $request)
    {
        var_dump("USE TRY-CATCH TO HANDLE ERRORS");

        return $this->defaultResolver->getController($request);
    }

    public function getArguments(Request $request, $controller)
    {
        return $this->defaultResolver->getArguments($request, $controller);
    }
}
