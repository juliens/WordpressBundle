<?php

namespace Generic\WordpressBundle\Security;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Alptis\Sf2\Bundle\Ariane\GenericBundle\Services\KeyValueStoreSf2SessionAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Role\Role;

class WordpressAuthenticationProvider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $redirect_url = null;
    private $wordpress_loader_id = null;
    private $service_container = null;

    public function __construct(UserProviderInterface $userProvider, $service_container, $wordpress_loader_id, $redirect_url)
    {
        $this->wordpress_loader_id = $wordpress_loader_id;
        $this->service_container = $service_container;
        $this->redirect_url = $redirect_url;
    }

    public function authenticate(TokenInterface $token)
    {

        $this->service_container->get($this->wordpress_loader_id)->load();
        $user = wp_get_current_user();
        if (isset($user->data) && isset($user->data->user_nicename)) {
            $token->setUser($user->data->user_email);
            $token->setAuthenticated(true);
            //$token->setRoles(array());
        } else {
            $token->setRedirectUrl($this->redirect_url);
        }
        return $token;
    }


    public function supports(TokenInterface $token)
    {
        return $token instanceof WordpressToken;
    }
}
