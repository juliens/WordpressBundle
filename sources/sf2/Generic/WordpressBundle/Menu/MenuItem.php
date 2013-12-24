<?php

namespace Generic\WordpressBundle\Menu;

class MenuItem
{
    private $parent = null;
    private $childs = array();

    private $id = null;
    private $type = null;
    private $url = null;
    private $title = null;
    private $active = false;
    private $active_child = null;

    public function __construct($menu_post)
    {
        $this->id = $menu_post->ID;
        $this->type = $menu_post->object;
        $this->url = $menu_post->url;
        $this->title = $menu_post->title;
    }

    public function setParent(MenuItem $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }


    public function addChild(MenuItem $child)
    {
        $this->childs[] = $child;
        return $this;
    }

    public function getChilds()
    {
        return $this->childs;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getActiveChild()
    {
        return $this->active_child;
    }

    public function setActiveChild(MenuItem $child)
    {
        if (!in_array($child, $this->childs)) {
            throw new \Exception('Cannot set non child menuitem has active child');
        }
        $this->active_child = $child;
        $this->announceActivationToParent();
    }

    public function announceActivationToParent()
    {
        if ($this->hasParent()) {
            $this->getParent()->setActiveChild($this);
        }
    }

    public function hasParent()
    {
        return $this->parent!==null;
    }

    public function setActive()
    {
        $this->active = true;
        $this->announceActivationToParent();
    }

    public function isActive()
    {
        return $this->active;
    }

    public function hasActiveChild()
    {
        return ($this->getActiveChild()!=null);
    }

}

