<?php

namespace Generic\WordpressBundle\Extension;

use Twig_Token;

class WordpressTitleTokenParser extends \Twig_TokenParser
{
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideWordpressTitleEnd'), true);
        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
        return new WordpressTitleNode($body, $lineno);
    }

    public function decideWordpressTitleEnd(\Twig_Token $token)
    {
        return $token->test('endwordpress_title');
    }

    public function getTag()
    {
        return 'wordpress_title';
    }
}
