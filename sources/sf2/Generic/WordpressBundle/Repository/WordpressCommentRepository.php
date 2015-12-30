<?php

namespace Generic\WordpressBundle\Repository;


class WordpressCommentRepository
{
    const ITEM_PER_PAGE = 20;

    private $wordpress_loader;

    public function __construct($wordpress_loader)
    {
        $this->wordpress_loader = $wordpress_loader;
        $this->wordpress_loader->load();
    }

    public function findByPostId($post_id, $status = 'approve')
    {
        $query = new \WP_Comment_Query;
        return $query->query(array('post_id' => $post_id, 'status' => $status));
    }
}

