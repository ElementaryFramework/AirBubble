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

use ElementaryFramework\AirBubble\Tokens\BlockToken;
use ElementaryFramework\AirBubble\Tokens\ConditionToken;
use ElementaryFramework\AirBubble\Tokens\DataTableToken;
use ElementaryFramework\AirBubble\Tokens\ForeachToken;
use ElementaryFramework\AirBubble\Tokens\ForToken;
use ElementaryFramework\AirBubble\Tokens\IncludeToken;
use ElementaryFramework\AirBubble\Tokens\InputLabelToken;
use ElementaryFramework\AirBubble\Tokens\SelectItemsToken;
use ElementaryFramework\AirBubble\Tokens\TextToken;
use ElementaryFramework\AirBubble\Renderer\Template;

/**
 * Template namespaces registry
 *
 * Store all defaults and user namespaces
 * used by the parser.
 *
 * @category Util
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Util/NamespacesRegistry
 */
abstract class NamespacesRegistry
{
    /**
     * The namespaces registry.
     *
     * This variable is initialized with default
     * AirBubble's namespaces.
     *
     * @var array
     */
    private static $_registry = array(
        "b:"  => Template::SCHEMA_URI
    );

    /**
     * Adds a namespace to the registry.
     *
     * The name must be a valid namespace name and have to
     * end with <b>:</b>.
     *
     * @example <code>NamespacesRegistry::add("ns:", "http://domain.com/schema")</code>
     *
     * @param string $name The namespace name
     * @param string $uri  The schema URI
     *
     * @return void
     */
    public static function add(string $name, string $uri)
    {
        self::$_registry[$name] = $name;
    }

    /**
     * Gets a namespace from the registry by its name.
     *
     * @param string $name The namespace name.
     *
     * @return string
     */
    public static function get(string $name): string
    {
        return self::exists($name) ? self::$_registry[$name] : null;
    }

    /**
     * Removes a namespace in the registry by its name.
     *
     * @param string $name The name of the token.
     *
     * @return void
     */
    public static function remove(string $name)
    {
        if (self::exists($name)) {
            unset(self::$_registry[$name]);
        }
    }

    /**
     * Checks if the given namespace exists in the registry.
     *
     * @param string $name The name of the element.
     *
     * @return boolean
     */
    public static function exists(string $name): bool
    {
        return array_key_exists($name, self::$_registry);
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
