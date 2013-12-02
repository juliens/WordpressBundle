<?php

namespace Generic\WordpressBundle\Services;

use Generic\WordpressBundle\Repository\Post;

interface Metabox
{
    public function wordpress_process(\WP_Post $post);

    public function process(Post $post);

    public function getId();

    public function getTitle();

    public function getType();

    public function getPosition();

    public function getPriority(); 
}

