<?php

namespace Generic\WordpressBundle\Repository;

class Post
{
    private $wp_post;
    private $metas = array();
    private $type = 'post';
    private $tags;
    private $linkImg = '';

    public function getWPPost()
    {
        return $this->wp_post;
    }

    public function getMetas()
    {
        return $this->metas;
    }

    public function getMeta($key)
    {
        $metas = $this->getMetas();
        if (isset($metas[$key])) {
            return $metas[$key];
        }
        return null;
    }

    public function setMeta($key, $value)
    {
        if (!is_array($value)) {
            $value = array($value);
        }
        $this->metas[$key] = $value;
        return $this;
    }

    public function getLinkImg(){
        return $this->linkImg;
    }

    public function __construct($post = null) 
    {
        $this->wp_post = $post;
        if ($post===null) {
            $this->wp_post = new \WP_Post(new \stdClass());
        }
        if (isset($this->wp_post->post_type)) {
            $this->type = $this->wp_post->post_type;
        }
        if ($this->wp_post->ID!=null) {
            $this->metas = get_metadata('post', $this->wp_post->ID);
            if(!empty($this->metas["_thumbnail_id"])){
                $idAttachment = $this->getMeta("_thumbnail_id");
                if(!empty($idAttachment)){
                    $link = wp_get_attachment_image_src($idAttachment[0]);
                    $this->linkImg = $link[0];
                }
            }
        }
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        $this->wp_post->post_type = $type;
        return $this;
    }


    public function __call($function, $args)
    {
        if (substr($function,0,3)=='get') {
            return $this->get(substr($function,3));
        }
        if (substr($function,0,3)=='set' && isset($args[0])) {
            return $this->set(substr($function,3), $args[0]);
        }
    }

    public function getId()
    {
        return $this->wp_post->ID;
    }

    public function setId($id)
    {
        $this->wp_post->ID = $id;
        return $this;
    }

    public function get($key)
    {
        if (!isset ($this->wp_post->$key)) {
            throw new \Exception("la propriété $key n'existe pas"); 
        }
        return $this->wp_post->$key;
    }

    public function set($key, $value)
    {
        if (!isset ($this->wp_post->$key)) {
            throw new \Exception("la propriété $key n'existe pas"); 
        }
        $this->wp_post->$key = $value;
        return $this;

    }

    public function getTags()
    {
        if (is_null($this->tags)) {
            $this->tags = array();
            // tags
            $tags = wp_get_post_tags($this->wp_post->ID);
            foreach($tags as $tag) {
                $this->tags[] = (array) $tag;
            }
        }
        return $this->tags;
    }

    public function trash()
    {
        return wp_trash_post($this->wp_post->ID);
    }
}
