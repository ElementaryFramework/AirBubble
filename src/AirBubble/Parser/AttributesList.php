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

namespace AirBubble\Parser;

use AirBubble\Attributes\IAttribute;

/**
 * Attributes List
 *
 * Represent a list of attributes in a token.
 *
 * @category Parser
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Parser/AttributesList
 * @final
 */
final class AttributesList implements \Iterator, \SeekableIterator, \Countable, \Serializable, \ArrayAccess
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
     * @var IAttribute[]
     */
    private $_attributes;

    /**
     * AttributesList constructor
     *
     * @param IAttribute[] $attributes The list of attributes
     */
    public function __construct(array $attributes = array())
    {
        $this->_setAttributesList($attributes);
    }

    /**
     * Changes the value of the wrapped list.
     *
     * @param IAttribute[] $attributes The list of tokens.
     *
     * @return void
     */
    private function _setAttributesList(array $attributes)
    {
        $this->_attributes = $attributes;
    }

    /**
     * Checks if the key can be accessed.
     *
     * @return bool
     */
    public function valid()
    {
        return array_key_exists($this->_key, $this->_attributes);
    }

    /**
     * Returns the current attribute.
     *
     * @return IAttribute
     * @throws \Exception
     */
    public function current()
    {
        return $this->_attributes[$this->_key];
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
     * @return IAttribute
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
     * Counts attributes
     *
     * @return int
     */
    public function count()
    {
        $counter = 0;
        foreach ((array)$this->_attributes as $_key => $value) {
            if (is_int($_key)) {
                ++$counter;
            }
        }
        return $counter;
    }

    /**
     * Serializes attributes list
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->_attributes);
    }

    /**
     * Unserialize attributes list
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
        return array_key_exists($offset, $this->_attributes);
    }

    /**
     * Return the result at the given offset
     *
     * @param int $offset
     *
     * @return IAttribute
     * @throws \Exception
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->_attributes[$offset];
        } else {
            throw new Exception("Index {$offset} out of range of the token list.");
        }
    }

    /**
     * Changes the result value at the given offset
     *
     * @param int $offset
     * @param IAttribute $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->_attributes[$offset] = $value;
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
            unset($this->_attributes[$offset]);
        }
    }

    /**
     * Adds a token to the end of the list
     *
     * @param IAttribute $token The Token to add
     *
     * @return void
     */
    public function add(IAttribute $token)
    {
        array_push($this->_attributes, $token);
    }

    /**
     * Parses all attributes in the list.
     *
     * @return void
     */
    public function parse()
    {
        foreach ($this->_attributes as &$attr) {
            $attr->parse();
        }
        unset($attr);
    }
}
