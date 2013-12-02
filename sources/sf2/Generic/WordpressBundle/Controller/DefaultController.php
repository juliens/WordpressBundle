<?php

namespace Generic\WordpressBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('GenericWordpressBundle:Default:index.html.twig', array('name' => $name));
    }

    public function wordpressAction(Request $request)
    {
        $this->get('wordpress.loader')->load();
        $this->get('wordpress.loader')->loadPostInWordpressFromRequest($request);
        remove_theme_support('custom-header');
        remove_action( 'wp_head',             'wp_enqueue_scripts',              1     );
        remove_action( 'wp_head',             'feed_links',                      2     );
        remove_action( 'wp_head',             'feed_links_extra',                3     );
        remove_action( 'wp_head',             'rsd_link'                               );
        remove_action( 'wp_head',             'wlwmanifest_link'                       );
        remove_action( 'wp_head',             'adjacent_posts_rel_link_wp_head', 10, 0 );
        remove_action( 'wp_head',             'locale_stylesheet'                      );
        remove_action( 'wp_head',             'noindex',                          1    );
        remove_action( 'wp_head',             'wp_print_styles',                  8    );
        remove_action( 'wp_head',             'wp_print_head_scripts',            9    );
        remove_action( 'wp_head',             'wp_generator'                           );
        remove_action( 'wp_head',             'rel_canonical'                          );
        remove_action( 'wp_head',             'wp_shortlink_wp_head',            10, 0 );
        ob_start();
        @wp_head();
        $head = ob_get_clean();
        ob_start();
        $content = dynamic_sidebar( 'sidebar-2' );
        $side = ob_get_clean();
        ob_start();
        the_content();
        $content = ob_get_clean();
        $post = array('content'=>$content);
        return $this->render($this->container->getParameter('wordpress.template'), array ('post'=>$post));
    }
}
