<?php

namespace Generic\WordpressBundle\Services;

use Symfony\Component\HttpFoundation\Request;
use Generic\WordpressBundle\Menu\MenuItem;
use Generic\WordpressBundle\Menu\Menu;


class WordpressLoader
{
    private $wordpress_location = null;
    private $shortcodes = array();
    private $metaboxes = array();
    private $admin_menus = array();
    private $wordpress_shortcode_loader = null;
    private $wordpress_metabox_loader = null;
    private $wordpress_admin_menus_loader = null;
    private $wordpress_post_factory = null;
    private $metaboxes_already_load = false;
    private $admin_already_load = false;
    private $metaboxes_loader_already_add = false;
    private $title = null;
    private $title_is_loaded = false;
    private $head_is_loaded = false;
    private $head = null;
    private $post_loaded = false;
    private $menu_loader_already_add  = false;
    private $menus = array();

    public function __construct($wordpress_location, $wordpress_shortcode_loader, $wordpress_metabox_loader, $wordpress_admin_menus_loader, $wordpress_post_factory)
    {
        $this->wordpress_location = $wordpress_location;
        $this->wordpress_shortcode_loader = $wordpress_shortcode_loader;
        $this->wordpress_metabox_loader = $wordpress_metabox_loader;
        $this->wordpress_admin_menu_loader = $wordpress_admin_menus_loader;
        $this->wordpress_post_factory = $wordpress_post_factory;
    }

    public function getMenu($name)
    {
        if(array_key_exists($name, $this->menus)) {
            return $this->menus[$name];
        } else {
            $this->load();
            if (!$menu = wp_get_nav_menu_object($name)) {
                throw new \Exception('Erreur menu introuvable');
            }

            $items = wp_get_nav_menu_items($menu->term_id);
            $all_items_inline = array();
            $root_items = array ();
            foreach ($items as $item) {
                $objectItem = new MenuItem($item);
                if ($item->menu_item_parent!=0) {
                    if (!isset($all_items_inline[$item->menu_item_parent])) {
                        throw new \Exception('Erreur parent introuvable');
                    }
                    $objectItem->setParent($all_items_inline[$item->menu_item_parent]);
                    $objectItem->getParent()->addChild($objectItem);
                }
                $all_items_inline[$objectItem->getId()] = $objectItem;
                if ($objectItem->getParent()===null) {
                    $root_items[] = $objectItem;
                }
            }

            $this->menus[$name] = new Menu($menu->term_id, $root_items);

            return $this->menus[$name];
        }
    }

    public function isLoaded()
    {
        return function_exists('wp_head');
    }

    public function load()
    {
        if ($this->isLoaded()===false) {
            require $this->wordpress_location.'/wp-load.php';
            foreach (get_defined_vars() as $key=>$value) {
                $GLOBALS[$key] = $value;
            }
        }
        $this->loadShortcodes();
        if (!$this->metaboxes_loader_already_add) {
            add_action( 'add_meta_boxes', array($this, 'loadMetaboxes'));
            $this->metaboxes_loader_already_add = true;
        }
        if (!$this->menu_loader_already_add) {
            add_action( 'admin_menu', array($this, 'loadAdminMenus'));
            $this->menu_loader_already_add = true;
        }
    }

    public function getCurrentPost()
    {
        if (isset($GLOBALS['post'])) {
            return $this->wordpress_post_factory->createFromWP_Post($GLOBALS['post']);
        } else {
            return false;
        }
    }

    public function loadMetaboxes()
    {
        $this->metaboxes_already_load = true;
        $this->wordpress_metabox_loader->loadMetaboxes($this->metaboxes);
    }

    public function loadShortcodes()
    {
        $this->wordpress_shortcode_loader->loadShortcodes($this->shortcodes);
    }

    public function addShortcode(Shortcode $shortcode)
    {
        $this->shortcodes[] = $shortcode;
        if ($this->isLoaded()) {
            $this->wordpress_shortcode_loader->loadShortcode($shortcode);
        }
    }

    public function addMetabox(Metabox $metabox)
    {
        $this->metaboxes[] = $metabox;
        if ($this->metaboxes_already_load) {
            $this->wordpress_metabox_loader->loadMetabox($metabox);
        }
    }

    public function loadPostInWordpressFromRequest(Request $request)
    {
        $post_id = url_to_postid($request->getPathInfo());
        $post_id = ($post_id!=null) ? $post_id : url_to_postid('index.php'.$request->getPathInfo());
        if ($post_id == null) {
            return false;
        }
        return $this->loadPostInWordpressFromPostId($post_id);

    }

    public function loadPostInWordpressFromPostId($post_id)
    {
        $this->post_loaded = true;
        $GLOBALS['wp_the_query']->query('page_id='.$post_id);
        $posts = $GLOBALS['wp_the_query']->get_posts();
        if (count($posts)==0) {
            throw new \Exception('Impossible de trouver le post courant');
        }
        $GLOBALS['post'] = $posts[0];
        return true;
    }

    public function getTitle()
    {
        if ($this->title_is_loaded===false) {
            $this->title_is_loaded = true;
            if ($this->post_loaded) {
                ob_start();
                the_title();
                $this->title = ob_get_clean();
            }
        }
        return $this->title;
    }

    public function getHead()
    {
        if ($this->head_is_loaded===false) {
            $this->head_is_loaded = true;
            if ($this->post_loaded) {
                ob_start();
                wp_head();
                $this->head = ob_get_clean();
            }
        }
        return $this->head;
    }

    public function getContent()
    {
        //A vérifier mais si on appel pas wp_head le content est vide
        $this->getHead();
        return get_the_content();
    }

    public function getShortcodes()
    {
        return $this->shortcodes;
    }

    public function addAdminMenu(AdminMenu $menu)
    {
        $this->admin_menus[] = $menu;
        if ($this->admin_already_load) {
            $this->wordpress_admin_menus_loader->loadAdminMenu($menu);
        }
    }

    public function loadAdminMenus()
    {
        $this->menu_loader_already_add = true;
        $this->wordpress_admin_menu_loader->loadAdminMenus($this->admin_menus);
    }

    public function getPageAdminUrl($page_id)
    {
        return get_option('siteurl').'/wp-admin/post.php?post='.$page_id.'&action=edit';
    }

}
