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

namespace Bubble\Util;

use Bubble\Tokens\IToken;

/**
 * Select Items List
 *
 * Represent a list of items to use with b:selectItems.
 *
 * @category Parser
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Util/SelectItemsList
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
