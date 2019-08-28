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

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMNode;

use ElementaryFramework\AirBubble\Renderer\Template;

abstract class BaseDirective
{
    /**
     * The DOM node of the directive.
     *
     * @var DOMAttr
     */
    private $_domAttr;

    /**
     * The DOM node of the element.
     *
     * @var DOMAttr
     */
    private $_domElement;

    /**
     * The DOM document
     *
     * @var DOMDocument
     */
    private $_domDocument;

    /**
     * The template in which the directive reside.
     *
     * @var Template
     */
    protected $template;

    /**
     * Creates a new instance of an AirBubble template's
     * directive.
     *
     * @param DOMAttr $attr
     * @param DOMDocument $document
     */
    public function __construct(DOMAttr $attr, DOMElement &$element, DOMDocument &$document, Template &$template)
    {
        $this->_domAttr = $attr;
        $this->_domElement = $element->cloneNode(true);
        $this->_domDocument = $document;
        $this->template = $template;
    }

    /**
     * Returns the value of the directive.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_domAttr->value;
    }

    /**
     * Returns the element of the directive
     *
     * @return \DOMElement
     */
    public function getElement()
    {
        return $this->_domElement;
    }

    /**
     * Process the directive and return the
     * output node.
     *
     * @return DOMNode|null
     */
    abstract function process(): ?DOMNode;
}