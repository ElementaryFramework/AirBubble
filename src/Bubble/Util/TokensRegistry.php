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

use Bubble\Tokens\ConditionToken;
use Bubble\Tokens\DataTableToken;
use Bubble\Tokens\ForeachToken;
use Bubble\Tokens\InputLabelToken;
use Bubble\Tokens\SelectItemsToken;
use Bubble\Tokens\TextToken;

/**
 * Template tokens registry
 *
 * Store all defaults and user tokens
 * used by the parser.
 *
 * @category Util
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Util/TokensRegistry
 */
class TokensRegistry
{
    /**
     * The tokens registry.
     *
     * This variable is initialized with default
     * Bubble's tokens.
     *
     * @var array
     */
    private static $_registry = array(
        "b:text" => TextToken::class,
        "b:inputLabel" => InputLabelToken::class,
        "b:condition" => ConditionToken::class,
        "b:foreach" => ForeachToken::class,
        "b:dataTable" => DataTableToken::class,
        "b:selectItems" => SelectItemsToken::class
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
    public static function add(string $elementName, $elementClass)
    {
        self::$_registry[$elementName] = $elementClass;
    }

    public static function get(string $elementName)
    {
        return self::$_registry[$elementName];
    }

    public static function remove(string $elementName)
    {
        unset(self::$_registry[$elementName]);
    }

    public static function exists(string $elementName): bool
    {
        return array_key_exists($elementName, self::$_registry);
    }

    public static function registry()
    {
        return self::$_registry;
    }
}
