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

namespace ElementaryFramework\AirBubble\Directives;

use ElementaryFramework\AirBubble\Exception\ParseErrorException;
use ElementaryFramework\AirBubble\Util\NamespacesRegistry;

/**
 * Elif Directive
 *
 * Represent the <b>elif</b> directive.
 *
 * @category Attributes
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Attributes/ElifDirective
 */
class ElifDirective extends IfDirective
{
    /**
     * The name of this attribute;
     */
    public const NAME = "elif";

    /**
     * Process the directive and return the
     * output node.
     *
     * @return DOMNode|null
     */
    public function process(): ?\DOMNode
    {
        $element = $this->getElement();

        $node = $this->_getPreviousSiblingOf($element);

        while ($node->hasAttributeNS(NamespacesRegistry::get("b:"), "elif")) {
            $directive = new ElifDirective($node->getAttributeNodeNS(NamespacesRegistry::get("b:"), "elif"), $node, $this->document, $this->template);
            if ($directive->process() === null) {
                $node = $this->_getPreviousSiblingOf($node);
            } else {
                return null;
            }
        }

        if ($node !== null && $node->hasAttributeNS(NamespacesRegistry::get("b:"), "if")) {
            $directive = new IfDirective($node->getAttributeNodeNS(NamespacesRegistry::get("b:"), "if"), $node, $this->document, $this->template);
            return ($directive->process() === null) ? $this->render() : null;
        } else {
            throw new ParseErrorException("b:elif directive used without a b:if on the previous node.");
        }
    }

    private function _getPreviousSiblingOf(\DOMNode $node): ?\DOMNode
    {
        $previous = $node;
        do {
            $previous = $previous->previousSibling;
            if ($previous === null) break;
        } while ($previous->nodeType === XML_TEXT_NODE);
        return $previous;
    }
}
