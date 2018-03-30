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

use Bubble\Exception\InvalidDataException;
use Bubble\Exception\UnknownFunctionException;

/**
 * Eval sandbox
 *
 * Evaluate code in a sandbox.
 *
 * @category Util
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Util/EvalSandBox
 */
class EvalSandBox
{
    /**
     * Functions context class to use
     *
     * @var string
     */
    private static $_functionContext = "\Bubble\Util\FunctionsContext";

    /**
     * Changes the current functions context.
     *
     * @param $context
     *
     * @return void
     * @throws InvalidDataException When the given context is not a subclass of \Bubble\Data\FunctionsContext
     */
    public static function setFunctionsContext(string $context): void
    {
        if (!is_subclass_of($context, "\Bubble\Data\FunctionsContext")) {
            throw new InvalidDataException(
                "The given functions context is not a subclass of \Bubble\Data\FunctionsContext."
            );
        }

        self::$_functionContext = $context;
    }

    public static function eval(string $code)
    {
        $code = self::_parseCode($code);
        $context = self::$_functionContext;

        return eval(
            "\$context = new {$context}; return {$code};"
        );
    }

    private static function _parseCode(string $code): string
    {
        return preg_replace_callback("#@(\w+)\\(#U", function ($m) {
            if (!method_exists(self::$_functionContext, $m[1])) {
                throw new UnknownFunctionException($m[1]);
            }

            return str_replace("@{$m[1]}(", "\$context->{$m[1]}(", $m[0]);
        }, $code);
    }
}
