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

namespace ElementaryFramework\AirBubble\Tokens;

use ElementaryFramework\AirBubble\Exception\UnexpectedTokenException;
use ElementaryFramework\AirBubble\Parser\AttributesList;
use ElementaryFramework\AirBubble\Renderer\Template;

/**
 * Base Token
 *
 * Base implementation for all tokens.
 *
 * @category Tokens
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Tokens/BaseToken
 */
abstract class BaseToken implements IToken
{
    /**
     * Token path.
     *
     * @var string
     */
    protected $_path;

    /**
     * The list of attributes in this token.
     *
     * @var AttributesList
     */
    protected $_attributes;

    /**
     * The DOM document.
     *
     * @var \DOMDocument
     */
    protected $_document;

    /**
     * The DOM element.
     *
     * @var \DOMElement
     */
    protected $_element;

    /**
     * The template in which this token exists
     *
     * @var Template
     */
    protected $_template;

    /**
     * Token constructor
     *
     * @param \DOMElement $element
     * @param \DOMDocument $document
     *
     * @throws UnexpectedTokenException
     */
    public function __construct(\DOMElement $element, \DOMDocument &$document)
    {
        $this->_element = $element;
        $this->_document =& $document;
        $this->_path = $element->getNodePath();
        $this->_attributes = new AttributesList();

        $this->_parseAttributes();
    }

    /**
     * Parses attributes for this element.
     *
     * @return mixed|void
     *
     * @throws UnexpectedTokenException
     */
    abstract protected function _parseAttributes();

    /**
     * Changes the template file in which this
     * token have to be rendered.
     *
     * @param Template $template The template file to use.
     */
    public function setTemplate(Template &$template)
    {
        $this->_template = &$template;
    }

    /**
     * Gets the path to this token
     * in the DOM template.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->_path;
    }

    /**
     * Gets the list of attributes in
     * this token.
     *
     * @return AttributesList
     */
    public function getAttributes(): AttributesList
    {
        return $this->_attributes;
    }

    /**
     * Replaces the current token's element
     * with a new one.
     *
     * @param \DOMNode $newNode The new node to use.
     * @return void
     */
    protected function _replace(\DOMNode $newNode)
    {
        if ($this->_element !== null && $newNode !== null) {
            $this->_element->parentNode->replaceChild($newNode, $this->_element);
        }
    }
}
