<?php

namespace Generic\WordpressBundle\Services;

class SymfonyAdminMenu implements AdminMenu
{
    public function getId()
    {
        return 'symfony_admin_menu';
    }

    public function getMenuItemName()
    {
        return 'Symfony 2';
    }

    public function getMenuItemImageUrl()
    {
        return null;
    }

    public function getTitlePage()
    {
        return 'Symfony 2 Administration Page';
    }

    public function process()
    {
        echo "<h2>Administration Symfony 2</h2>";
        echo "Bientot ici, la gestion/activation de vos shortcode, menu_admin, metabox symfony";
    }

    public function getPosition()
    {
        return 110;
    }

    public function getCapability()
    {
        return 'manage_options';
    }
}
