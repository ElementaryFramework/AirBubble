<?php

/**
 * Bubble - A PHP template engine
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category  Library
 * @package   Bubble
 * @author    Axel Nana <ax.lnana@outlook.com>
 * @copyright 2018 Aliens Group, Inc.
 * @license   LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
 * @version   GIT: 0.0.1
 * @link      http://bubble.na2axl.tk
 */

namespace Bubble\Parser;

use Bubble\Exception\TemplateNotFoundException;
use Bubble\Exception\UnknownTokenException;
use Bubble\Renderer\Template;
use Bubble\Tokens\IToken;
use Bubble\Tokens\TextToken;
use Bubble\Exception\ParseErrorException;
use Bubble\Util\TokensRegistry;

/**
 * Template tokenizer
 *
 * Parse template files in a set of tokens. Generate the result
 * as a Template class object.
 *
 * @category Parser
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Parser/Tokenizer
 */
class Tokenizer
{
    /**
     * Tokens list.
     *
     * @var TokensList
     */
    private $_tokens;

    /**
     * DOM document object.
     *
     * @var \DOMDocument
     */
    private $_dom;

    /**
     * Tokenizer constructor
     */
    private function __construct()
    {
        libxml_use_internal_errors(true);

        $this->_tokens = new TokensList();
        $this->_dom = new \DOMDocument("1.0", "utf-8");
    }

    private function _load(string $content)
    {
        $this->_dom->loadXML($content);

        if ($this->_dom->documentElement->nodeName !== "b:bubble") {
            throw new ParseErrorException("The \"b:bubble\" tag have to be at the root of your document.");
        }
    }

    private function _tokenize()
    {
        if ($this->_dom->hasChildNodes()) {
            $this->_tokenizeElement($this->_dom->documentElement);
        }
    }

    private function _tokenizeElement(\DOMElement $element)
    {
        foreach ($element->childNodes as $e) {
            if ($e->hasChildNodes()) {
                $this->_tokenizeElement($e);
            }

            if (strpos($e->nodeName, "b:") === 0) {
                $this->_tokens->add($this->_toToken($e));
            }
        }
    }

    private function _toToken(\DOMElement $element): IToken
    {
        $token = null;

        if ($element->nodeName === "b:bubble") {
            throw new ParseErrorException("The \"b:bubble\" tag have to be used once at the root of your document");
        }

        if (TokensRegistry::exists($element->nodeName)) {
            $class = TokensRegistry::get($element->nodeName);
            $token = new $class($element, $this->_dom);
        }

        if ($token === null) {
            throw new UnknownTokenException($element->nodeName);
        }

        return $token;
    }

    public static function tokenize(string $content): Tokenizer
    {
        $parser = new Tokenizer;
        $parser->_load($content);

        $parser->_tokenize();

        return $parser;
    }

    /**
     * Gets DOM document object.
     *
     * @return  \DOMDocument
     */
    public function getDom()
    {
        return $this->_dom;
    }

    /**
     * Get tokens list.
     *
     * @return  TokensList
     */
    public function getTokens()
    {
        return $this->_tokens;
    }
}
