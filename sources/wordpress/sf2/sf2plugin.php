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
                if ($kernel==null) {
                    $loader = require_once __DIR__.'/../../../..//app/bootstrap.php.cache';
                    require_once  __DIR__.'/../../../../app/AppKernel.php';
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
            $this->loadSf2();
        }

        public function get($id)
        {
            return $this->getContainer()->get($id);
        }
    }



    $sf2plugin = new Sf2Plugin();
