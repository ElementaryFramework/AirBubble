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

namespace Bubble\Data;

use Bubble\Exception\InvalidQueryException;
use Bubble\Util\KeyValuePair;

/**
 * Template data model
 *
 * Stores all template's data
 *
 * @category Data
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Data/DataModel
 */
class DataModel
{
    /**
     * Data store.
     *
     * @var KeyValuePair[]
     */
    private $_data;

    public function __construct()
    {
        $this->_data = array();
    }

    public function add(KeyValuePair $data)
    {
        $this->_data[$data->getKey()] = $data;
    }

    public function set(string $key, $value)
    {
        $this->_data[$key] = new KeyValuePair($key, $value);
    }

    public function get(string $key): KeyValuePair
    {
        if (array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        }

        throw new InvalidQueryException($key);
    }
}
