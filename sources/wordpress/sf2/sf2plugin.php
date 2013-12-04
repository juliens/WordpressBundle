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
                    $env = (get_option('symfony2_env'));
                    $url = (get_option('symfony2_url'));
                    if ($path==null) {
                        $path = __DIR__.'/../../../../';
                        update_option('symfony2_path', $path);
                    }
                    if ($env==null) {
                        $env = 'dev';
                        update_option('symfony2_env', $env);
                    }

                if (!file_exists($path.'app/bootstrap.php.cache')) {
                    add_action( 'admin_footer', array( $this, 'symfony2_warning' ) );
                    return;
                }


                if ($kernel==null) {
                    $loader = require_once $path.'app/bootstrap.php.cache';
                    require_once  $path.'app/AppKernel.php';
                    $kernel = new AppKernel($env, true);
                    $kernel->loadClassCache();
                    $kernel->boot();
                    $this->kernel = $kernel;
                    $this->container = $kernel->getContainer();
                    $this->container->get('session')->set('test','test');
                } else {
                    $this->kernel = $kernel;
                    $this->container = $kernel->getContainer();
                }
                if ($url!=null) {
                    preg_match('%([^:]*):\/\/([^\/]*)(\/.*)%', $url, $matches);
                    if (count($matches)==4) {
                        $context = $this->container->get('router')->getContext();
                        $context->setHost($matches[2]);
                        $context->setScheme($matches[1]);
                        $context->setBaseUrl($matches[3]);
                    } else {
                        add_action( 'admin_footer', array( $this, 'symfony2_url_warning' ) );

                    }
                }
                $wp_loader = $this->container->get('wordpress.loader');
                $wp_loader->load();
                

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
            add_action( 'admin_menu', array ($this, 'menu_params') );
            add_action('admin_init', array($this, 'admin_init'));

            $this->loadSf2();

        }

        public function admin_init()
        {
            //add_management_page( 'Custom Permalinks', 'Custom Permalinks', 'manage_options', 'my-unique-identifier', 'custom_permalinks_options_page' );
            if ($this->container!=null) {
                $shortcodes = $this->getContainer()->get('wordpress.loader')->getShortcodes();
                foreach ($shortcodes as $shortcode) {
                    register_setting('wp_symfony_settings', 'shortcode_'.$shortcode->getName());
                }
            }
            register_setting('wp_symfony_settings', 'symfony2_path');
            register_setting('wp_symfony_settings', 'symfony2_url');
            register_setting('wp_symfony_settings', 'symfony2_env');
        }

        public function menu_params()
        {
            add_options_page( 'Symfony2 configuration','Symfony2','manage_options','options_symfony2', array( $this, 'settings_symfony2' ) );
            //add_menu_page('Symfony2 configuration', 'Symfony2', 'manage_options', 'symfony2_options', array($this, 'menu_params_page'), plugins_url( 'sf2/images/icon.png' ), 6 ); 
        }

        public function settings_symfony2()
        {
            include(dirname(__FILE__).'/settings.php');
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
