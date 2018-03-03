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

use Bubble\Tokens\IToken;
use Bubble\Renderer\Template;
use Bubble\Tokens\ForeachToken;

/**
 * Token List
 *
 * Represent a list of tokens.
 *
 * @category Parser
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Parser/Tokenizer
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

    public function setTemplate(Template &$template)
    {
        foreach ($this->_tokens as &$tok) {
            $tok->setTemplate($template);
        }
        unset($tok);
    }
}
