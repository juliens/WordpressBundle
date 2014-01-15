<?php

namespace Generic\WordpressBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\User\UserInterface;

class WordpressToken extends AbstractToken
{
    private $redirectUrl;

    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }


    public function __construct()
    {
        $this->setAuthenticated(false);
        parent::__construct();
    }

    public function getCredentials()
    {
        return null;
    }



}
