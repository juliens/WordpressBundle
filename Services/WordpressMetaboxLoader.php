<?php

namespace Generic\WordpressBundle\Services;

class WordpressMetaboxLoader
{
    private $loadedMetabox = array();

    private function assertCanAddMetabox()
    {
        if (function_exists('add_meta_box')===false) {
            throw new \Exception('Impossible d\'ajouter un shortcode, avez vous bien chargé Wordpress');
        }
    }

    public function loadMetaboxes(array $metaboxes)
    {
        $this->assertCanAddMetabox();
        foreach ($metaboxes as $metabox) {
            if (!isset($this->loadedMetabox[$metabox->getId()]) || $this->loadMetabox[$metabox->getId()]===false) {
                add_meta_box($metabox->getId(),$metabox->getTitle(),array($metabox, 'wordpress_process'), $metabox->getType(), $metabox->getPosition(), $metabox->getPriority());
                $this->loadMetabox[$metabox->getId()] = true;
            }
        }
    }

    public function loadMetabox(Metabox $metabox)
    {
        $this->loadMetabox(array($metabox));
    }
}
