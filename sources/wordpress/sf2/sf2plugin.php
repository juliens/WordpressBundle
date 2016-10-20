<?php
/*
Plugin Name: Integration Symfony 2
Author: J. Salleyron
Version: 1.0
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Sf2Plugin
{
    private $container = null;
    private $kernel = null;
    private $in_sf2 = true;

    private function getDirCacheSymfony($dir)
    {
        $dirCache = 'app';
        if(is_dir($dir.'/var')){
            $dirCache = 'var';
        }
        return $dirCache; 
    }
    private function isValidSymfonyPath($dir)
    {        
        return (file_exists($dir . $this->getDirCacheSymfony($dir) .'/bootstrap.php.cache'));
    }

    private function calculatePath()
    {
        $dir = __DIR__;
        if (($pos = strpos($dir, 'vendor')) !== false) {
            //Lien symbolique dans les vendor

            $dir = substr($dir, 0, $pos);
            if (!$this->isValidSymfonyPath($dir)) {
                return null;
            }
            return $dir;
        } elseif (($pos = strpos($dir, 'wordpress/wp-content')) !== false) {
            $dir = substr($dir, 0, $pos);
            if (!$this->isValidSymfonyPath($dir)) {
                return null;
            }
            return $dir;
        }
    }

    private function calculateUrl()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if (($pos = strpos($url, 'wordpress/wp-admin')) != false) {
            $url = substr($url, 0, $pos) . 'web/app_dev.php';
        }
        return $url;
    }

    private function loadSf2()
    {
        global $kernel;
        //@settings_fields('wp_symfony_settings');
        //@do_settings_fields('wp_symfony_settings');
        $path = (get_option('symfony2_path'));
        $env = (get_option('symfony2_env'));
        $url = (get_option('symfony2_url'));

        if (!$this->isValidSymfonyPath($path)) {
            add_action('admin_footer', array($this, 'symfony2_warning'));
            return;
        }

        if ($kernel == null) {
            
            $dircache =  $this->getDirCacheSymfony($path);
            $loader = require_once $path . $dircache .'/bootstrap.php.cache';
            if($dircache == 'var'){
                $autoload = require_once $path . 'app/autoload.php';
            }
            require_once $path . 'app/AppKernel.php';
            $debug = true;
            if ($env == 'prod') {
                $debug = false;
            }
            $kernel = new AppKernel($env, $debug);
            $kernel->loadClassCache();
            $kernel->boot();
            $this->kernel = $kernel;
            $this->container = $kernel->getContainer();
            if ($this->container->get('session')->isStarted() == false) {
                $this->container->get('session')->start();
            }
            if ($url != null) {
                $this->overloadUrlContext($url);
            }
        } else {
            $this->kernel = $kernel;
            
            if ($this->kernel instanceof AppCache) {
                if (!is_subclass_of($this->kernel->getKernel(), 'Symfony\Component\HttpKernel\KernelInterface')) {
                    throw new RuntimeException("Le kernel doit implémenter Symfony\Component\HttpKernel\KernelInterface");
                }
                $this->kernel = $this->kernel->getKernel();
            }

            if (!is_subclass_of($this->kernel, 'Symfony\Component\HttpKernel\KernelInterface')) {
                throw new RuntimeException("Le kernel doit implémenter Symfony\Component\HttpKernel\KernelInterface");
            }

            $this->container = $this->kernel->getContainer();
        }
        $wp_loader = $this->container->get('wordpress.loader');
        $wp_loader->load();

        $request = Request::createFromGlobals();
        $response = new Response();
        $kernel->terminate($request, $response);

    }

    private function overloadUrlContext($url)
    {
        preg_match('%([^:]*):\/\/([^\/]*)(\/?.*)%', $url, $matches);
        if (count($matches) == 4) {
            $context = $this->container->get('router')->getContext();
            $context->setHost($matches[2]);
            $context->setScheme($matches[1]);
            $context->setBaseUrl($matches[3]);
        } else {
            add_action('admin_footer', array($this, 'symfony2_url_warning'));

        }
    }

    public function symfony2_url_warning()
    {
        echo "<div id='message' class='error'>";
        echo "La configuration de votre url de symfony2 est incorrect. Vous devez mettre quelque chose du style http://serveur/path/vers/app.php";
        echo "</div>";
    }
    public function symfony2_warning()
    {
        echo "<div id='message' class='error'>";
        echo "La configuration de votre path vers symfony2 est incorrect. Vous devez mettre quelque chose comme /home/moi/monsite/app/";
        echo "</div>";
    }
    public function getContainer()
    {
        return $this->container;
    }

    public function __construct()
    {
        //register_activation_hook( __FILE__, array($this, 'activate'));
        add_action('activate_sf2/sf2plugin.php', array($this, 'activate'));
        add_action('admin_menu', array($this, 'menu_params'));
        add_action('admin_init', array($this, 'admin_init'));

        $this->loadSf2();

    }

    public function activate()
    {
        if (get_option('symfony2_path') == null) {
            update_option('symfony2_path', $this->calculatePath());
        }
        if (get_option('symfony2_env') == null) {
            update_option('symfony2_env', 'dev');
        }
        if (get_option('symfony2_url') == null) {
            update_option('symfony2_url', $this->calculateUrl());
        }

    }

    public function admin_init()
    {
        //add_management_page( 'Custom Permalinks', 'Custom Permalinks', 'manage_options', 'my-unique-identifier', 'custom_permalinks_options_page' );
        if ($this->container != null) {
            $shortcodes = $this->getContainer()->get('wordpress.loader')->getShortcodes();
            foreach ($shortcodes as $shortcode) {
                register_setting('wp_symfony_settings', 'shortcode_' . $shortcode->getName());
            }
        }
        register_setting('wp_symfony_settings', 'symfony2_path');
        register_setting('wp_symfony_settings', 'symfony2_url');
        register_setting('wp_symfony_settings', 'symfony2_env');
    }

    public function menu_params()
    {
        add_options_page('Symfony2 configuration', 'Symfony2', 'manage_options', 'options_symfony2', array($this, 'settings_symfony2'));
        //add_menu_page('Symfony2 configuration', 'Symfony2', 'manage_options', 'symfony2_options', array($this, 'menu_params_page'), plugins_url( 'sf2/images/icon.png' ), 6 );
    }

    public function settings_symfony2()
    {
        include dirname(__FILE__) . '/settings.php';
    }

    public function menu_params_page()
    {
        if ($this->container != null) {
            $shortcodes = $this->getContainer()->get('wordpress.loader')->getShortcodes();
        }
        include dirname(__FILE__) . '/settings.php';
    }

    public function get($id)
    {
        return $this->getContainer()->get($id);
    }
}

$sf2plugin = new Sf2Plugin();
