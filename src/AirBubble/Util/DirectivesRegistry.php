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

namespace ElementaryFramework\AirBubble\Util;

use ElementaryFramework\AirBubble\Directives\IfDirective;

/**
 * Template directives registry
 *
 * Store all defaults and user defined directives
 * used by the parser.
 *
 * @category Util
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Util/DirectivesRegistry
 */
abstract class DirectivesRegistry
{
    /**
     * The directives registry.
     *
     * This variable is initialized with default
     * AirBubble's directives.
     *
     * @var array
     */
    private static $_registry = array(
        "b:if" => IfDirective::class
    );

    /**
     * Adds an attribute to the registry.
     *
     * The name must be a valid element name and have to start with
     * a namespace registered in the {@see NamespacesRegistry}.
     * The attribute must be the class name, which implements the
     * <b>IAttribute</b> interface.
     *
     * @example <code>DirectivesRegistry::add("ns:myAttr", MyAttribute::class)</code>
     *
     * @param string $elementName The attribute's name
     * @param string $elementClass The class' name
     *
     * @return void
     */
    public static function add(string $elementName, string $elementClass)
    {
        self::$_registry[$elementName] = $elementClass;
    }

    /**
     * Gets an attribute from the registry by its name.
     *
     * @param string $elementName The name of the attribute.
     *
     * @return string
     */
    public static function get(string $elementName): string
    {
        return self::exists($elementName) ? self::$_registry[$elementName] : null;
    }

    /**
     * Removes an attribute in the registry by its name.
     *
     * @param string $elementName The name of the attribute.
     *
     * @return void
     */
    public static function remove(string $elementName)
    {
        if (self::exists($elementName)) {
            unset(self::$_registry[$elementName]);
        }
    }

    /**
     * Checks if the given element exists in the registry.
     *
     * @param string $elementName The name of the element.
     *
     * @return bool
     */
    public static function exists(string $elementName): bool
    {
        return array_key_exists($elementName, self::$_registry);
    }

    /**
     * Returns the whole registry.
     *
     * @return array
     */
    public static function registry(): array
    {
        return self::$_registry;
    }
}
