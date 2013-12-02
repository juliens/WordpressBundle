<?php

namespace Generic\WordpressBundle\Listener;

use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RouterListener extends RouterListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        try {
            parent::onKernelRequest($event);
        } catch (NotFoundHttpException $e) {
            
        }
    }
}
