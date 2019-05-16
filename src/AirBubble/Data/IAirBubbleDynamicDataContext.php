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

/**
 * AirBubble's template dynamic data context interface
 *
 * Represents an interface to create a dynamic template's data context. Dynamic data context are able to call methods
 * access properties which are not defined in the object.
 *
 * @category Data
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Data/IAirBubbleDynamicDataContext
 */
interface IAirBubbleDynamicDataContext extends IAirBubbleDataContext
{
    /**
     * Method called when the DataResolver try to find a dynamic property.
     *
     * @param string $name The name of the dynamic property.
     *
     * @return mixed
     */
    function getBubbleProperty(string $name);

    /**
     * Method called when the DataResolver try to find a dynamic property.
     *
     * @param string $name  The name of the dynamic indexed property.
     * @param mixed  $index The index to acces in the property.
     *
     * @return mixed
     */
    function getBubbleIndexedProperty(string $name, $index);

    /**
     * Method called when the DataResolver try to find a dynamic method.
     *
     * @param string $name       The name of the dynamic method.
     * @param array  $parameters The set of paramters of the dynamic method.
     *
     * @return mixed
     */
    function callBubbleMethod(string $name, array $parameters);
}
