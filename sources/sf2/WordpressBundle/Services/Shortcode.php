<?php

namespace Generic\WordpressBundle\Services;

interface Shortcode
{
    public function getName();

    public function process();
}
