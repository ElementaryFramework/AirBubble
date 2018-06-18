<?php

/**
 * AirBubble - A PHP template engine
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @category  Library
 * @package   AirBubble
 * @author    Axel Nana <ax.lnana@outlook.com>
 * @copyright 2018 Aliens Group, Inc.
 * @license   MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @version   GIT: 1.1.0
 * @link      http://bubble.na2axl.tk
 */

namespace ElementaryFramework\AirBubble\Parser;

use ElementaryFramework\AirBubble\Exception\ParseErrorException;
use ElementaryFramework\AirBubble\Exception\UnknownTokenException;
use ElementaryFramework\AirBubble\Tokens\IToken;
use ElementaryFramework\AirBubble\Util\TokensRegistry;

/**
 * Template tokenizer
 *
 * Parse template files in a set of tokens. Generate the result
 * as a Template class object.
 *
 * @category Parser
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Parser/Tokenizer
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
