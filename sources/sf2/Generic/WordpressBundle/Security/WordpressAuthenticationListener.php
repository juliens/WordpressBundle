<?php

namespace Generic\WordpressBundle\Security;

use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Generic\WordpressBundle\Security\WordpressToken;


class WordpressAuthenticationListener implements ListenerInterface
{
    protected $tokenStorage;
    protected $authenticationManager;

    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        try {
            $response = $this->authenticationManager->authenticate(new WordpressToken());
            if ($response->isAuthenticated()) {
                $this->tokenStorage->setToken($response);
                $session = $request->getSession();
                $token_id = uniqid();
                $session->set('token_id', $token_id);
                $session->set($token_id, $response);
            } elseif ($response->getRedirectUrl()!=null) {
                $url = $response->getRedirectUrl();
                if (strpos('?',$url)!==false) {
                    $separator = '&';
                } else {
                    $separator = '?';
                }
                $url .= $separator.'redirect_to='.urlencode($request->getUri());
                $response = new RedirectResponse($url);
                $event->setResponse($response);
            } else {
                $response = new Response();
                $response->setStatusCode(403);
                $event->setResponse($response);
            }
        } catch (AuthenticationException $e) {
            $response = new Response();
            $response->setStatusCode(403);
            $event->setResponse($response);
        }
    }
}
