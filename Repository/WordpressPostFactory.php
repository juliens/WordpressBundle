<?php

namespace Generic\WordpressBundle\Repository;

class WordpressPostFactory
{
    public function createFromWP_Post(\WP_Post $post)
    {
        $toReturn = null;
        switch ($post->post_type) {
        default:
            $toReturn = new Post($post);
        }
        return $toReturn;
    }

    public function create ($type)
    {
        $post = new Post();
        $post->setType($type);
        $post->setcomment_status('closed');
        $post->setping_status('closed');
        $post->setpost_status('publish');

        return $post;
        
    }
}
