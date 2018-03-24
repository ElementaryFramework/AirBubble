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

use Bubble\Renderer\Template;

/**
 * Template Extender
 *
 * Process extends between templates.
 *
 * @category Util
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Util/TemplateExtender
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

    public static function getParentTemplate(string $template): string
    {
        $parent = self::getParentName($template);
        $path = Utilities::resolveTemplate($parent);

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

        foreach ($blocks as $name => $block) {
            $nodes = $domP->getElementsByTagNameNS(Template::SCHEMA_URI, "block");
            foreach ($nodes as $node) {
                if ($node->hasAttribute("name") && $node->getAttribute("name") === $name) {
                    $parent = $node->parentNode;
                    Utilities::insertHTMLBefore(Utilities::innerHTML($block), $node);
                    $parent->removeChild($node);
                }
            }
        }

        return $domP->saveXML();
    }
}
