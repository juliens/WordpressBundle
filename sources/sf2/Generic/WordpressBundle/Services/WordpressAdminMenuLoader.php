<?php

namespace Generic\WordpressBundle\Services;

class WordpressAdminMenuLoader
{
    private $loadedMenu = array();

    public function loadAdminMenu(AdminMenu $menu)
    {
        $this->loadAdminMenus(array($menu));
    }

    public function loadAdminMenus($menus)
    {
        foreach ($menus as $menu) {
            if (!isset($this->loadedMenu[$menu->getId()]) || $this->loadedMenu[$menu->getId()]===false) {
                $this->loadAdminMenu[$menu->getId()] = true;
                add_menu_page($menu->getTitlePage(), $menu->getMenuItemName(), $menu->getCapability(), $menu->getId(), array($menu, 'process'), $menu->getMenuItemImageUrl(), $menu->getPosition() );
            }
        }
    }
}
