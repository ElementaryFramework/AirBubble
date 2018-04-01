<?php

/**
 * Bubble - A PHP template engine
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
 * @package   Bubble
 * @author    Axel Nana <ax.lnana@outlook.com>
 * @copyright 2018 Aliens Group, Inc.
 * @license   MIT <https://github.com/na2axl/bubble/blob/master/LICENSE>
 * @version   GIT: 1.1.0
 * @link      http://bubble.na2axl.tk
 */

namespace Bubble\Data;

use Bubble\Exception\KeyNotFoundException;
use Bubble\Exception\PropertyNotFoundException;
use Bubble\Exception\InvalidQueryException;

/**
 * Template data resolver
 *
 * Resolves data from queries in templates.
 *
 * @category Data
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/na2axl/bubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Data/DataResolver
 */
class DataResolver
{
    /**
     * The data model to use when
     * resolving queries.
     *
     * @var DataModel
     */
    private $_model;

    /**
     * Backup for already resolved values.
     * This is used to speed up resolving process.
     *
     * @var array
     */
    private $_resolvedBackup;

    /**
     * DataResolver constructor
     *
     * @param DataModel $model The data model to use when resolving queries.
     */
    public function __construct(DataModel $model)
    {
        $this->_model = $model;
        $this->_resolvedBackup = array();
    }

    /**
     * Gets the data from.
     *
     * @param string $query
     *
     * @return object
     *
     * @throws InvalidQueryException
     * @throws KeyNotFoundException
     * @throws PropertyNotFoundException
     */
    public function resolve(string $query)
    {
        if (!array_key_exists($query, $this->_resolvedBackup)) {
            $parts = explode(".", $query);

            preg_match("#(\\w+)\\[([\w\d]+)\\]#", $parts[0], $matches);
            $isIndexedArray = count($matches) > 0;

            if ($isIndexedArray) {
                array_shift($parts);
                array_unshift($parts, $matches[2]);
                array_unshift($parts, $matches[1]);
            }

            $data = $this->_model->get($parts[0])->getValue();
            $parts = array_slice($parts, 1);

            if (count($parts) > 0) {
                foreach ($parts as $part) {
                    if (is_array($data)) {
                        if (!array_key_exists($part, $data)) {
                            throw new KeyNotFoundException($part, $query);
                        }
                        $data = $data[$part];
                    } elseif ($data instanceof \ArrayAccess) {
                        if (!$data->offsetExists($part)) {
                            throw new KeyNotFoundException($part, $query);
                        }
                        $data = $data[$part];
                    } elseif ($data instanceof IBubbleDataContext) {
                        $params = array();
                        preg_match("#(\\w+)\\((.+)\\)#isU", $part, $matches);
                        $isMethodCall = count($matches) > 0;
                        if ($isMethodCall) {
                            $params = array_map(function($item) {
                                return trim($item, "\"' ");
                            }, explode(",", $matches[2]));
                            $part = $matches[1];
                        }

                        $part = trim($part, "()");
                        preg_match("#(\\w+)\\[([\w\d]+)\\]#", $part, $matches);
                        $isIndexedArray = count($matches) > 0;
                        $part = $isIndexedArray ? $matches[1] : $part;

                        if (!property_exists($data, $part) && !method_exists($data, $part)) {
                            throw new PropertyNotFoundException($part, $query);
                        }
                        $data = $isIndexedArray ? ($data->$part)[$matches[2]] : (is_callable(array($data, $part)) ? call_user_func_array(array($data, $part), $params) : $data->$part);
                    } else {
                        throw new InvalidQueryException($query);
                    }
                }
            }

            $this->_resolvedBackup[$query] = $data;
        }

        return $this->_resolvedBackup[$query];
    }
}
