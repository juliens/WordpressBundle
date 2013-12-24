<?php

namespace Generic\WordpressBundle\Menu;

class Menu 
{
    private $items = array();
    private $term_id = null;

    public function __construct($term_id, $items)
    {
        $this->term_id = $term_id;
        $this->items = $items;
    }

    public function getMenuItems()
    {
        return $this->items;
    }

    public function setActive($post_id)
    {
        return $this->setActiveInMenuItems($post_id, $this->items);
    }

    private function setActiveInMenuItems($post_id, $menus)
    {
        foreach ($menus as $menu) {
            if ($menu->getId()==$post_id) {
                $menu->setActive();
                return true;
            }
            $has_active = $this->setActiveInMenuItems($post_id, $menu->getChilds());
            if ($has_active) {
                return true;
            }
        }
    }
}
