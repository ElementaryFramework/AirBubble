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

use ElementaryFramework\AirBubble\Data\DataResolver;
use ElementaryFramework\AirBubble\Util\Utilities;
use ElementaryFramework\AirBubble\Util\EvalSandBox;
use ElementaryFramework\AirBubble\Util\NamespacesRegistry;

/**
 * If Directive
 *
 * Represent the <b>if</b> directive.
 *
 * @category Attributes
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Attributes/IfDirective
 */
class IfDirective extends BaseDirective
{
    /**
     * The name of this attribute;
     */
    public const NAME = "if";

    /**
     * Evaluate the condition.
     *
     * @param DataResolver $resolver
     *
     * @return boolean
     */
    public function evaluate(DataResolver $resolver): bool
    {
        return EvalSandBox::eval(
            Utilities::populateData($this->getValue(), $resolver),
            $resolver
        );
    }

    /**
     * Process the directive and return the
     * output node.
     *
     * @return DOMNode|null
     */
    public function process(): ?\DOMNode
    {
        return $this->render();
    }

    /**
     * Renders the node.
     *
     * @return \DOMNode|null
     */
    protected function render(string $attr = "if"): ?\DOMNode
    {
        $element = $this->getElement()->cloneNode(true);
        $element->removeAttributeNode($element->getAttributeNodeNS(NamespacesRegistry::get("b:"), $attr));

        return $this->evaluate($this->template->getResolver())
            ? $element
            : null;
    }
}
