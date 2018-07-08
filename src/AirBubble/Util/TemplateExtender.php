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

use ElementaryFramework\AirBubble\Renderer\Template;
use ElementaryFramework\AirBubble\Data\DataResolver;

/**
 * Template Extender
 *
 * Process extends between templates.
 *
 * @category Util
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Util/TemplateExtender
 */
class TemplateExtender
{
    public static function isExtender(string $template): bool
    {
        $dom = Utilities::createDOMFromString($template);
        return $dom->documentElement->nodeName === "b:bubble" && $dom->documentElement->hasAttribute("extends");
    }

    public static function getParentName(string $template): string
    {
        $dom = Utilities::createDOMFromString($template);
        return $dom->documentElement->hasAttribute("extends") ? $dom->documentElement->getAttribute("extends") : null;
    }

    public static function getParentTemplate(string $template, DataResolver $resolver): string
    {
        $parent = self::getParentName($template);
        $path = Utilities::resolveTemplate($parent, $resolver);

        return file_get_contents($path);
    }

    public static function getTemplateBlocks(string $template): array
    {
        $blocks = array();

        $dom = Utilities::createDOMFromString($template);

        foreach ($dom->documentElement->childNodes as $node) {
            if ($node instanceof \DOMElement) {
                $blocks[$node->tagName] = $node;
            }
        }

        return $blocks;
    }

    public static function merge(string $parent, string $child): string
    {
        $blocks = self::getTemplateBlocks($child);

        $domP = Utilities::createDOMFromString($parent);

        self::_processReplaces($domP, $blocks);

        return $domP->saveXML();
    }

    private static function _processReplaces(\DOMDocument &$dom, array $blocks)
    {
        $nodes = $dom->getElementsByTagNameNS(Template::SCHEMA_URI, "block");
        $length = $nodes->length;

        foreach ($nodes as $node) {
            if ($node->hasAttribute("name") && array_key_exists($name = $node->getAttribute("name"), $blocks)) {
                Utilities::insertHTMLBefore(Utilities::innerHTML($blocks[$name]), $node);
                $node->parentNode->removeChild($node);
            }
        }

        if ($dom->getElementsByTagNameNS(Template::SCHEMA_URI, "block")->length < $length) {
            self::_processReplaces($dom, $blocks);
        }
    }
}
