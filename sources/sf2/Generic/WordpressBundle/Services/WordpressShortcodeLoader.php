<?php

namespace Generic\WordpressBundle\Services;

class WordpressShortcodeLoader
{
    private $loadedShortcode = array();

    private function assertCanAddShortcode()
    {
        if (function_exists('add_shortcode')===false) {
            throw new \Exception('Impossible d\'ajouter un shortcode, avez vous bien chargé Wordpress');
        }
    }

    public function loadShortcodes(array $shortcodes)
    {
        $this->assertCanAddShortcode();
        foreach ($shortcodes as $shortcode) {
            if (!isset($this->loadedShortcode[$shortcode->getName()]) || $this->loadShortcodes[$shortcode->getName()]===false) {
                add_shortcode($shortcode->getName(), array($shortcode, 'process'));
                $this->loadShortcode[$shortcode->getName()] = true;
            }
        }
    }

    public function loadShortcode(Shortcode $shortcode)
    {
        $this->loadShortcodes(array($shortcode));
    }
}
