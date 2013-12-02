<?php

namespace Generic\WordpressBundle\Extension;

class WordpressTitleNode extends \Twig_Node
{
    public function __construct($body, $lineno)
    {
        parent::__construct(array('body'=>$body), array(), $lineno, 'wordpress_title');
    }


    public function compile(\Twig_Compiler $compiler)
    {
            $compiler
                ->write("if (isset(\$context['wordpress_loader']) && \$context['wordpress_loader']->getTitle()!=null) {\n")
                ->indent()
                ->write("echo \$context['wordpress_loader']->getTitle();\n")
                ->outdent()
                ->write("} else {\n")
                ->indent()
                ->subcompile($this->getNode('body'))
                ->outdent()
                ->write("}\n")
                ;
    }
}
