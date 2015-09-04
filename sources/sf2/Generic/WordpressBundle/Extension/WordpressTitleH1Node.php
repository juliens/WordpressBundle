<?php

namespace Generic\WordpressBundle\Extension;

class WordpressTitleH1Node extends \Twig_Node
{
    public function __construct($body, $lineno)
    {
        parent::__construct(array('body'=>$body), array(), $lineno, 'wordpress_h1');
    }


    public function compile(\Twig_Compiler $compiler)
    {
            $compiler
                ->write("if (isset(\$context['wordpress_loader']) && \$context['wordpress_loader']->getH1()!=null) {\n")
                ->indent()
                ->write("echo \$context['wordpress_loader']->getH1();\n")
                ->outdent()
                ->write("} else {\n")
                ->indent()
                ->subcompile($this->getNode('body'))
                ->outdent()
                ->write("}\n")
                ;
    }
}
