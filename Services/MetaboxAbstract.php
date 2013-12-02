<?php

namespace Generic\WordpressBundle\Services;

use Generic\WordpressBundle\Repository\Post;

abstract class MetaboxAbstract implements Metabox
{
    private $wordpress_factory = null;

    public function __construct($wordpress_factory)
    {
        $this->wordpress_factory = $wordpress_factory;
    }

    public function wordpress_process(\WP_Post $post)
    {
        return $this->process($this->wordpress_factory->createFromWP_Post($post));
    }

    abstract public function process(Post $post);

    abstract public function getId();

    abstract public function getTitle();

    public function getType()
    {
        return 'post';
    }

    public function getPosition()
    {
        return 'normal';
    }


    public function getPriority()
    {
        return 'default';
    }
}

