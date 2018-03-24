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

namespace Bubble\Tokens;

use Bubble\Parser\AttributesList;
use Bubble\Renderer\Template;

/**
 * Base Token
 *
 * Base implementation for all tokens.
 *
 * @category Tokens
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Tokens/BaseToken
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
