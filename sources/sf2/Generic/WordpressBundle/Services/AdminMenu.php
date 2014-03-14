<?php

namespace Generic\WordpressBundle\Services;

interface AdminMenu
{
    public function getId();
    public function getMenuItemName();
    public function getMenuItemImageUrl();
    public function getTitlePage();
    public function process();
    public function getPosition();
    public function getCapability();

}
