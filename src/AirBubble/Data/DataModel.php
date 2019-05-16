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

namespace ElementaryFramework\AirBubble\Data;

use ElementaryFramework\AirBubble\Exception\InvalidQueryException;
use ElementaryFramework\AirBubble\Util\KeyValuePair;

/**
 * Template data model
 *
 * Stores all template's data
 *
 * @category Data
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Data/DataModel
 */
class DataModel
{
    /**
     * Data store.
     *
     * @var KeyValuePair[]
     */
    private $_data;

    /**
     * DataModel constructor.
     */
    public function __construct()
    {
        $this->_data = array();
    }

    /**
     * Adds a template's data with a KeyValuePair.
     *
     * @param KeyValuePair $data The template's data to add
     */
    public function add(KeyValuePair $data)
    {
        $this->_data[$data->getKey()] = $data;
    }

    /**
     * Sets a template's data with the given key.
     *
     * @param string $key   The key of the data.
     * @param mixed $value The template's data associated to
     * the key.
     */
    public function set(string $key, $value)
    {
        $this->_data[$key] = new KeyValuePair($key, $value);
    }

    /**
     * Gets a template's data from the key.
     *
     * @param string $key The key of the data.
     *
     * @return KeyValuePair
     *
     * @throws InvalidQueryException
     */
    public function get(string $key): KeyValuePair
    {
        if (array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        }

        throw new InvalidQueryException($key);
    }

    /**
     * Creates a copy of this instance.
     *
     * @return DataModel
     */
    public function copy(): DataModel
    {
        $dataModel = new DataModel();
        $dataModel->_data = $this->_data;

        return $dataModel;
    }
}
