<?php

namespace Generic\WordpressBundle\Extension;

class WordpressExtension extends \Twig_Extension
{
    private $wordpress_loader = null;

    public function __construct($wordpress_loader)
    {
        $this->wordpress_loader = $wordpress_loader;
    }

    public function getName()
    {
        return 'wordpress_extension';
    }

    public function getFunctions()
    {
        return array(
            'wordpress_head'=>new \Twig_Function_Method($this, 'WordpressHead', array('is_safe'=>array('html')))
        );
    }

    
    public function WordpressHead()
    {
        if ($this->wordpress_loader->isLoaded() && $this->wordpress_loader->getHead()!=null) {
            return $this->wordpress_loader->getHead();
        }
        return null;

    }

    public function getTokenParsers()
    {
        return array(
            new WordpressTitleTokenParser(),
            new WordpressTitleH1TokenParser(),
        );
    }

    public function getGlobals()
    {
        $globals = parent::getGlobals();
        $globals['wordpress_loader'] = $this->wordpress_loader;
        return $globals;
    }
    
}

