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

use Bubble\Tokens\TextToken;
use Bubble\Tokens\InputLabelToken;
use Bubble\Bubble;
use Bubble\Data\DataResolver;
use Bubble\Renderer\Template;

/**
 * Utilities
 *
 * A set of commonly used methods.
 *
 * @category Util
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/na2axl/bubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Util/Utilities
 */
class Utilities
{
    public static function toString($value)
    {
        if (is_bool($value)) {
            $value = $value ? "true" : "false";
        }

        return strval($value);
    }

    public static function innerHTML(\DOMNode $element): string
    {
        $innerHTML = "";
        $children  = $element->childNodes;

        foreach ($children as $child) {
            $innerHTML .= $element->ownerDocument->saveXML($child);
        }

        return $innerHTML;
    }

    public static function appendHTML(\DOMNode $parent, string $html)
    {
        $tmpDoc = new \DOMDocument();
        $tmpDoc->loadXML("<wrapper>{$html}</wrapper>");
        foreach ($tmpDoc->documentElement->childNodes as $node) {
            $node = $parent->ownerDocument->importNode($node, true);
            $parent->appendChild($node);
        }
    }

    public static function insertHTMLBefore(string $html, \DOMNode $refNode)
    {
        $tmpDoc = new \DOMDocument();
        $tmpDoc->loadXML("<wrapper xmlns:b=\"" . Template::SCHEMA_URI . "\">{$html}</wrapper>");
        foreach ($tmpDoc->documentElement->childNodes as $node) {
            $node = $refNode->ownerDocument->importNode($node, true);
            $refNode->parentNode->insertBefore($node, $refNode);
        }
    }

    public static function resolveTemplate(string $path)
    {
        $config = Bubble::getConfiguration();
        return realpath($config->getTemplatesBasePath() . DIRECTORY_SEPARATOR . $path);
    }

    public static function populateData(string $templatePart, DataResolver $resolver)
    {
        do {
            $templatePart = preg_replace_callback(Template::DATA_MODEL_QUERY_REGEX, function ($m) use ($resolver) {
                return self::toString($resolver->resolve($m[1]));
            }, $templatePart);
        } while(preg_match(Template::DATA_MODEL_QUERY_REGEX, $templatePart, $matches));

        $templatePart = preg_replace_callback(Template::EXPRESSION_REGEX, function ($m) {
            $res = EvalSandBox::eval($m[1]);
            return self::toString($res);
        }, $templatePart);

        return $templatePart;
    }

    public static function createDOMFromString(string $content): \DOMDocument
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $dom->loadXML($content);

        return $dom;
    }

    public static function computeOutputString(\DOMDocument $parsed)
    {
        $output = $parsed->saveXML();
        $outDOM = new \DOMDocument();
        $outDOM->loadXML($output);

        return $outDOM->saveHTML();
    }
}
