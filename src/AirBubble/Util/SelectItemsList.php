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

namespace ElementaryFramework\AirBubble\Util;

use ElementaryFramework\AirBubble\Tokens\IToken;

/**
 * Select Items List
 *
 * Represent a list of items to use with b:selectItems.
 *
 * @category Parser
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Util/SelectItemsList
 * @final
 */
final class SelectItemsList implements \Iterator, \SeekableIterator, \Countable, \Serializable, \ArrayAccess
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
     * @var KeyValuePair[]
     */
    private $_items;

    /**
     * TokensList constructor
     *
     * @param KeyValuePair[] $items The list of tokens
     */
    public function __construct(array $items = array())
    {
        $this->_setItemsList($items);
    }

    /**
     * Changes the value of the wrapped list.
     *
     * @param KeyValuePair[] $items The list of tokens.
     *
     * @return void
     */
    private function _setItemsList(array $items)
    {
        $this->_items = $items;
    }

    /**
     * Checks if the key can be accessed.
     *
     * @return bool
     */
    public function valid()
    {
        return array_key_exists($this->_key, $this->_items);
    }

    /**
     * Returns the current token.
     *
     * @return KeyValuePair
     */
    public function current()
    {
        return $this->_items[$this->_key];
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
     * @return void
     *
     * @throws \Exception
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
        return count($this->_items);
    }

    /**
     * Serializes tokens list
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->_items);
    }

    /**
     * Unserialize tokens list
     *
     * @param string $serialized
     *
     * @return SelectItemsList
     */
    public function unserialize($serialized)
    {
        $this->_setItemsList(unserialize($serialized));
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
        return array_key_exists($offset, $this->_items);
    }

    /**
     * Return the result at the given offset
     *
     * @param int $offset
     *
     * @return KeyValuePair
     *
     * @throws \Exception
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->_items[$offset];
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
        $this->_items[$offset] = $value;
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
            unset($this->_items[$offset]);
        }
    }

    /**
     * Adds an item to the end of the list
     *
     * @param KeyValuePair $item The item to add, in which
     * the key of the KeyValuePair represent the value of
     * the item, and the value of the KeyValuePair represent
     * the label of the item.
     *
     * @return void
     */
    public function add(KeyValuePair $item)
    {
        array_push($this->_items, $item);
    }

    /**
     * Adds an item to the end of the list
     *
     * @param string $value The value of the item.
     * @param object $label The label of the item.
     *
     * @return void
     */
    public function set(string $value, $label)
    {
        array_push($this->_items, new KeyValuePair($value, $label));
    }
}
