<?php

namespace Generic\WordpressBundle\Extension;

use Twig_Token;

class WordpressTitleH1TokenParser extends \Twig_TokenParser
{
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideWordpressH1End'), true);
        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
        return new WordpressTitleH1Node($body, $lineno);
    }

    public function decideWordpressH1End(\Twig_Token $token)
    {
        return $token->test('endwordpress_h1');
    }

    public function getTag()
    {
        return 'wordpress_h1';
    }
}
