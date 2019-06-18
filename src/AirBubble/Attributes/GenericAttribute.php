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

namespace ElementaryFramework\AirBubble\Attributes;

/**
 * Generic attribute
 *
 * Represent an HTML attribute or any other
 * attribute not supported by AirBubble.
 *
 * @category Attributes
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Attributes/GenericAttribute
 */
class GenericAttribute implements IAttribute
{
    /**
     * The value of the attribute.
     *
     * @var string
     */
    private $_value;

    /**
     * The DOMAttribute object
     *
     * @var \DOMAttr
     */
    private $_domAttribute;

    /**
     * The DOM document object.
     *
     * @var \DOMDocument
     */
    private $_document;

    /**
     * Gets the name of this attribute.
     *
     * @return string The name
     */
    public function getName(): string
    {
        return $this->_domAttribute->nodeName;
    }

    /**
     * ElementAttribute constructor.
     *
     * @param \DOMAttr $attr The DOM attribute.
     */
    public function __construct(\DOMAttr $attr, \DOMDocument &$document)
    {
        $this->_domAttribute = $attr;
        $this->_document = &$document;
    }

    /**
     * Gets the value to this attribute.
     *
     * @return string The value
     */
    public function getValue(): string
    {
        return $this->_value;
    }

    /**
     * Sets the value to this attribute.
     *
     * @param string $value The value
     */
    public function setValue(string $value)
    {
        $this->_value = $value;
    }

    /**
     * Parses the attribute.
     *
     * @return void
     */
    public function parse()
    {
        $this->_value = $this->_domAttribute->value;
    }

    /**
     * Renders this attribute.
     *
     * @return \DOMNode|null
     */
    public function render(): ?\DOMNode
    {
        return $this->_domAttribute;
    }
}
