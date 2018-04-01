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

namespace Bubble\Util;

use Bubble\Data\IBubbleDataContext;

/**
 * Key value pair
 *
 * Associate a key to a data.
 *
 * @category Util
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/na2axl/bubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Util/KeyValuePair
 */
class KeyValuePair implements IBubbleDataContext
{
    /**
     * Data key.
     *
     * @var string
     */
    private $_key;

    /**
     * Data value.
     *
     * @var object
     */
    private $_value;

    public function __construct(string $key, $value = null)
    {
        $this->_key = $key;
        $this->_value = $value;
    }

    /**
     * Gets the data key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Gets data value.
     *
     * @return object
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Sets data value.
     *
     * @param object $value Data value.
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->_value = $value;

        return $this;
    }
}
