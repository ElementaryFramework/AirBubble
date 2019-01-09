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
use ElementaryFramework\AirBubble\Tokens\AssignToken;
use ElementaryFramework\AirBubble\Tokens\ConditionToken;
use ElementaryFramework\AirBubble\Tokens\DataTableToken;
use ElementaryFramework\AirBubble\Tokens\ForeachToken;
use ElementaryFramework\AirBubble\Tokens\ForToken;
use ElementaryFramework\AirBubble\Tokens\IncludeToken;
use ElementaryFramework\AirBubble\Tokens\InputLabelToken;
use ElementaryFramework\AirBubble\Tokens\SelectItemsToken;
use ElementaryFramework\AirBubble\Tokens\TextToken;

/**
 * Template tokens registry
 *
 * Store all defaults and user tokens
 * used by the parser.
 *
 * @category Util
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Util/TokensRegistry
 */
abstract class TokensRegistry
{
    /**
     * The tokens registry.
     *
     * This variable is initialized with default
     * AirBubble's tokens.
     *
     * @var array
     */
    private static $_registry = array(
        "b:assign" => AssignToken::class,
        "b:include"  => IncludeToken::class,
        "b:text" => TextToken::class,
        "b:inputLabel" => InputLabelToken::class,
        "b:condition" => ConditionToken::class,
        "b:foreach" => ForeachToken::class,
        "b:dataTable" => DataTableToken::class,
        "b:selectItems" => SelectItemsToken::class,
        "b:for" => ForToken::class,
        "b:block" => BlockToken::class
    );

    /**
     * Adds a token to the registry.
     *
     * The name must be a valid element name and have to
     * start with the <b>b:</b> namespace.
     * The token must be the class name, which
     * implements the <b>IToken</b> interface.
     *
     * @example <code>TokensRegistry::add("b:myTag", MyTagToken::class)</code>
     *
     * @param string $elementName The token's name
     * @param string $elementClass The class' name
     *
     * @return void
     */
    public static function add(string $elementName, string $elementClass)
    {
        self::$_registry[$elementName] = $elementClass;
    }

    /**
     * Gets a token from the registry by its name.
     *
     * @param string $elementName The name of the token.
     *
     * @return string
     */
    public static function get(string $elementName): string
    {
        return self::exists($elementName) ? self::$_registry[$elementName] : null;
    }

    /**
     * Removes a token in the registry by its name.
     *
     * @param string $elementName The name of the token.
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
     * @return boolean
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
