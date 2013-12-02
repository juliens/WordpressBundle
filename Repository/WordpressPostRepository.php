<?php

namespace Generic\WordpressBundle\Repository;


class WordpressPostRepository
{
    private $wordpress_loader;
    private $wordpress_post_factory;

    public function __construct($wordpress_loader, $wordpress_post_factory)
    {
        $this->wordpress_loader = $wordpress_loader;
        $this->wordpress_loader->load();
        $this->wordpress_post_factory = $wordpress_post_factory;
    }
    public function get($post_id)
    {
        return $this->wordpress_post_factory(\WP_Post::get_instance($post_id));
    }

    public function save(Post $post)
    {
        $wp_error = null;
        if ($post->getID()!=null) {
            wp_update_post($post->getWPPost());
        } else {
            $post_id = wp_insert_post( $post->getWPPost(), $wp_error );          
            $post->setID($post_id);
        }
        $metas = get_metadata('post', $post->getId());
        foreach ($post->getMetas() as $key=>$value) {
            if (isset($metas[$key]) && $metas[$key]==$value) {
                continue;
            }
            foreach ($value as $subkey=>$subvalue) {
                if ($subkey==0) {
                    update_post_meta($post->getID(), $key, $subvalue);
                } else {
                    add_post_meta($post->getID(), $key, $subvalue);
                }
            }
        }
        return $this;
    }

    public function findPostByMeta($key, $value)
    {
        $query = $this->getMetaQuery($key, $value);
        return $this->getPostFromWordpressQuery($query);
    }

    private function getMetaQuery($key, $value)
    {
        return array(
            'meta_query' => array(
                array(
                    'key' => $key,
                    'value' => $value,
                )
            )
        );
    }

    public function findPageByMeta($key, $value)
    {
        $query = $this->getMetaQuery($key, $value);
        $query['post_type'] = 'page';
        return $this->getPostFromWordpressQuery($query);

    }

    private function getPostsFromWordpressQuery($query)
    {
        $query = new \WP_Query($query);
        $posts = $query->get_posts();
        $toReturn = array();
        foreach ($posts as $post) {
            $toReturn[] =  $this->wordpress_post_factory->createFromWP_Post($post);
        }
        return $toReturn;

    }

    private function getPostFromWordpressQuery($query)
    {
        $posts = $this->getPostsFromWordpressQuery($query);
        if (isset ($posts[0])) {
            return $posts[0];
        }
        return null;
    }
}

