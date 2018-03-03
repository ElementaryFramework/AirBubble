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

namespace Bubble\Attributes;

/**
 * Generic attribute
 *
 * Represent an HTML attribute or any other
 * attribute not supported by Bubble.
 *
 * @category Attributes
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Attributes/GenericAttribute
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
     * @return \DOMNode
     */
    public function render(): \DOMNode
    {
        return $this->_domAttribute;
    }
}
