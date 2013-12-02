<?php

namespace Generic\WordpressBundle\Services;

use Symfony\Component\HttpFoundation\Request;

class ShortcodeRender implements Shortcode
{
    private $template_helpers_action = null;
    private $router = null;

    public function __construct($template_helpers_action, $router)
    {
        $this->router = $router;
        $this->template_helpers_action = $template_helpers_action;
    }

    public function getName()
    {
        return "sf2_render";
    }

    public function process($params=array())
    {

        if (isset($params['_route'])) {
            $route = $this->router->getRouteCollection()->get($params['_route']);
            if ($route!=null) {
                $params = array_merge($route->getDefaults(), $params);
            }
        }
        return $this->render($params);
    }

    private function isControllerString($route)
    {
        return (count(explode(':', $route))>=3);
    }

    protected function render($params = array()) {
        if (!isset($params['_controller']) || !$this->isControllerString($params['_controller'])) {
            throw new \Exception('Vous n\'avez pas précisé de controller valide');
        }

        try {
            return $this->template_helpers_action->render($this->template_helpers_action->controller($params['_controller'], $params), $params);
        } catch (\LogicException $e) {
            global $kernel;
            $request = Request::createFromGlobals();
            $request = $request->duplicate(null, null, $params);
            $response = $kernel->handle($request);
            return $response->getContent();
        }

    }
}
