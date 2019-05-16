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
 * @version   1.4.0
 * @link      http://bubble.na2axl.tk
 */

namespace ElementaryFramework\AirBubble\Parser;

use ElementaryFramework\AirBubble\Renderer\Template;
use ElementaryFramework\AirBubble\Tokens\ForeachToken;
use ElementaryFramework\AirBubble\Tokens\IToken;

/**
 * Token List
 *
 * Represent a list of tokens.
 *
 * @category Parser
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Parser/Tokenizer
 * @final
 */
final class TokensList implements \Iterator, \SeekableIterator, \Countable, \Serializable, \ArrayAccess
{
    /**
     * Current key.
     *
     * @var int
     */
    private $_key = 0;

    /**
     * Wrapped array.
     *
     * @var IToken[]
     */
    private $_tokens;

    /**
     * TokensList constructor
     *
     * @param IToken[] $tokens The list of tokens
     */
    public function __construct(array $tokens = array())
    {
        $this->_setTokensList($tokens);
    }

    /**
     * Changes the value of the wrapped list.
     *
     * @param IToken[] $tokens The list of tokens.
     *
     * @return void
     */
    private function _setTokensList(array $tokens)
    {
        $this->_tokens = $tokens;
    }

    /**
     * Checks if the key can be accessed.
     *
     * @return bool
     */
    public function valid()
    {
        return array_key_exists($this->_key, $this->_tokens);
    }

    /**
     * Returns the current token.
     *
     * @return IToken
     * @throws \Exception
     */
    public function current()
    {
        return $this->_tokens[$this->_key];
    }

    /**
     * Seeks the internal pointer to the next value
     */
    public function next()
    {
        $this->_key++;
    }

    /**
     * Returns the value of the internal pointer
     *
     * @return int
     */
    public function key()
    {
        return $this->_key;
    }

    /**
     * Seeks the internal pointer to 0.
     */
    public function rewind()
    {
        $this->_key = 0;
    }

    /**
     * Seeks the internal pointer to a position
     *
     * @param int $position The position in the list
     *
     * @throws \Exception
     * @return IToken
     */
    public function seek($position)
    {
        $lastKey = $this->_key;
        $this->_key = $position;
        if (!$this->valid()) {
            $this->_key = $lastKey;
            throw new \Exception("Given index {$position} out of range of the token list.");
        }
    }

    /**
     * Counts tokens
     *
     * @return int
     */
    public function count()
    {
        return count($this->_tokens);
    }

    /**
     * Serializes tokens list
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->_tokens);
    }

    /**
     * Unserialize tokens list
     *
     * @param string $serialized
     *
     * @return TokensList
     */
    public function unserialize($serialized)
    {
        $this->_setTokensList(unserialize($serialized));
        return $this;
    }

    /**
     * Checks if a result exist at the given offset
     *
     * @param int $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_tokens);
    }

    /**
     * Return the result at the given offset
     *
     * @param int $offset
     *
     * @return IToken
     * @throws \Exception
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->_tokens[$offset];
        } else {
            throw new \Exception("Index {$offset} out of range of the token list.");
        }
    }

    /**
     * Changes the result value at the given offset
     *
     * @param int $offset
     * @param IToken $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->_tokens[$offset] = $value;
    }

    /**
     * Unset a result at the given offset
     *
     * @param int $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->_tokens[$offset]);
        }
    }

    /**
     * Adds a token to the end of the list
     *
     * @param IToken $token The Token to add
     *
     * @return void
     */
    public function add(IToken $token)
    {
        if ($token instanceof ForeachToken) {
            array_unshift($this->_tokens, $token);
        } else {
            array_push($this->_tokens, $token);
        }
    }

    /**
     * Parses all tokens in the list.
     *
     * @return void
     */
    public function parse()
    {
        foreach ($this->_tokens as &$tok) {
            $tok->parse();
        }
        unset($tok);
    }

    /**
     * Sets the template for each tokens in this list.
     *
     * @param Template $template
     */
    public function setTemplate(Template &$template)
    {
        foreach ($this->_tokens as &$tok) {
            $tok->setTemplate($template);
        }
        unset($tok);
    }

    /**
     * Checks if a token with the given stage exists in the list.
     *
     * @param int $tokenStage The token stage to search
     *
     * @return bool
     */
    public function hasTokenWithStage(int $tokenStage)
    {
        foreach ($this->_tokens as $token)
            if ($token->getStage() === $tokenStage)
                return true;

        return false;
    }

    /**
     * Returns the lowest token priority for the given token
     * stage in the list of tokens.
     *
     * @param int $tokenStage The token stage to search
     *
     * @return int
     */
    public function getLowestPriorityForStage(int $tokenStage)
    {
        $lowest = HIGHEST_TOKEN_PRIORITY;

        foreach ($this->_tokens as $token)
            if ($token->getStage() === $tokenStage && $token->getPriority() > $lowest)
                $lowest = $token->getPriority();

        return $lowest;
    }

    /**
     * Returns the highest token priority for the given token
     * stage in the list of tokens.
     *
     * @param int $tokenStage The token stage to search
     *
     * @return int
     */
    public function getHighestPriorityForStage(int $tokenStage)
    {
        $highest = LOWEST_TOKEN_PRIORITY;

        foreach ($this->_tokens as $token)
            if ($token->getStage() === $tokenStage && $token->getPriority() < $highest)
                $highest = $token->getPriority();

        return $highest;
    }
}
