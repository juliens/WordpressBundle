<?php 
    use Symfony\Component\HttpFoundation\Request;
		/*
		Plugin Name: Integration Symfony 2
		Author: J. Salleyron
		Version: 1.0
		*/

    class Sf2Plugin {
        private $container = null;
        private $kernel = null;
        private $in_sf2 = true;

        private function loadSf2()
        {
                global $kernel;
                    //@settings_fields('wp_symfony_settings');
                    //@do_settings_fields('wp_symfony_settings');
                    $path = (get_option('symfony2_path'));
                    if ($path==null) {
                        $path = __DIR__.'/../../../../';
                        update_option('symfony2_path', $path);
                    }

                if (!file_exists($path.'app/bootstrap.php.cache')) {
                    return;
                }

                if ($kernel==null) {
                    $loader = require_once $path.'app/bootstrap.php.cache';
                    require_once  $path.'app/AppKernel.php';
                    $kernel = new AppKernel('prod', true);
                    $kernel->loadClassCache();
                    $kernel->boot();
                    $this->kernel = $kernel;
                    $this->container = $kernel->getContainer();
                    $this->container->get('session')->set('test','test');
                } else {
                    $this->kernel = $kernel;
                    $this->container = $kernel->getContainer();
                }

                $wp_loader = $this->container->get('wordpress.loader');
                $wp_loader->load();
                

        }
        public function getContainer()
        {
            return $this->container;
        }

        public function __construct()
        {
            add_action( 'admin_menu', array ($this, 'menu_params') );
            add_action('admin_init', array($this, 'admin_init'));

            $this->loadSf2();

        }

        public function admin_init()
        {
            if ($this->container!=null) {
                $shortcodes = $this->getContainer()->get('wordpress.loader')->getShortcodes();
                foreach ($shortcodes as $shortcode) {
                    register_setting('wp_symfony_settings', 'shortcode_'.$shortcode->getName());
                }
            }
            register_setting('wp_symfony_settings', 'symfony2_path');
        }

        public function menu_params()
        {
            add_menu_page('Symfony2 configuration', 'Symfony2', 'manage_options', 'symfony2_options', array($this, 'menu_params_page'), plugins_url( 'sf2/images/icon.png' ), 6 ); 
        }

        public function menu_params_page()
        {
            if ($this->container!=null) {
                $shortcodes = $this->getContainer()->get('wordpress.loader')->getShortcodes();
            }
            include(dirname(__FILE__).'/settings.php');
        }

        public function get($id)
        {
            return $this->getContainer()->get($id);
        }
    }



    $sf2plugin = new Sf2Plugin();
