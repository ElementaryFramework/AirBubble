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

use Bubble\Exception\InvalidDataException;
use Bubble\Exception\UnknownFunctionException;
use Bubble\Data\DataResolver;
use Bubble\Renderer\Template;

/**
 * Eval sandbox
 *
 * Evaluate code in a sandbox.
 *
 * @category Util
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/na2axl/bubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Util/EvalSandBox
 */
class EvalSandBox
{
    /**
     * Functions context class to use
     *
     * @var string
     */
    private static $_functionContext = "\Bubble\Data\FunctionsContext";

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

    public static function eval(string $code, DataResolver $resolver)
    {
        $parsed = self::_parseCode($code, $resolver);
        $context = self::$_functionContext;

        return eval(
            "{$parsed[0]} \$context = new {$context}; return {$parsed[1]};"
        );
    }

    private static function _parseCode(string $code, DataResolver $resolver): array
    {
        $var_allocation = "";

        do {
            $code = preg_replace_callback(Template::DATA_MODEL_QUERY_REGEX, function ($m) use ($resolver, &$var_allocation) {
                $var_name = uniqid("tempvar_");
                $var_value = Utilities::toEvalSandBoxValue($resolver->resolve($m[1]));
                $var_allocation .= "\${$var_name} = {$var_value}; ";

                return "\${$var_name}";
            }, $code);
        } while (preg_match(Template::DATA_MODEL_QUERY_REGEX, $code, $matches));

        $code = preg_replace_callback("/@(\w+)\\(/U", function ($m) {
            if (!method_exists(self::$_functionContext, $m[1])) {
                throw new UnknownFunctionException($m[1]);
            }

            return str_replace("@{$m[1]}(", "\$context->{$m[1]}(", $m[0]);
        }, $code);

        return [$var_allocation, $code];
    }
}
