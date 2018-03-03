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
use Bubble\Attributes\ElementAttribute;
use Bubble\Attributes\ValueAttribute;
use Bubble\Attributes\GenericAttribute;
use Bubble\Attributes\ConditionAttribute;
use Bubble\Exception\UnexpectedTokenException;
use Bubble\Exception\ElementNotFoundException;
use Bubble\Util\Utilities;

/**
 * Condition Token
 *
 * Parse and render conditions.
 *
 * @category Tokens
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Tokens/ConditionToken
 */
class ConditionToken extends BaseToken
{
    /**
     * Token name.
     */
    public const NAME = "condition";

    /**
     * Token type.
     */
    public const TYPE = POST_PARSE_TOKEN;

    /**
     * Associates a node to a condition.
     *
     * @var array
     */
    private $_conditionsMap = array();

    private $_elsePath;

    protected function _parseAttributes()
    {
        if ($this->_element->hasAttributes()) {
            throw new UnexpectedTokenException("The \"condition\" element doesn't need any attribute.");
        }
    }

    /**
     * Gets the type of this token.
     *
     * @return integer
     */
    public function getType(): int
    {
        return self::TYPE;
    }

    /**
     * Gets the name of this token.
     *
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
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
     * Parses the token.
     *
     * @return void
     */
    public function parse()
    {
        if ($this->_element->hasChildNodes()) {
            $ifFound = false;
            $elseFound = false;
            foreach ($this->_element->childNodes as $element) {
                switch ($element->nodeName) {
                    case "if":
                    case "elseif":
                        if ($elseFound) {
                            throw new UnexpectedTokenException("Found another if/elseif statement after the else statement.");
                        }

                        if ($element->nodeName === "if") {
                            if ($ifFound) {
                                throw new UnexpectedTokenException("The \"condition\" attribute must have only one if statement.");
                            }
                            $ifFound = true;
                        } elseif (!$ifFound) {
                            throw new UnexpectedTokenException("Found elseif statement without or before if statement.");
                        }

                        if ($element->hasAttributes()) {
                            foreach ($element->attributes as $attr) {
                                switch ($attr->nodeName) {
                                    case "condition":
                                        $this->_conditionsMap[$element->getNodePath()] = new ConditionAttribute($attr, $this->_document);
                                        break;

                                    default:
                                        throw new UnexpectedTokenException("Only the \"condition\" attribute is allowed with the if statement.");
                                }
                            }
                        } else {
                            throw new ElementNotFoundException("The \"condition\" attribute is required with the if statement.");
                        }
                        break;

                    case "else":
                        if ($element->hasAttributes()) {
                            throw new UnexpectedTokenException("The else statement doesn't require any attribute.");
                        }

                        if (!$ifFound) {
                            throw new UnexpectedTokenException("Found else statement without or before if statement.");
                        }

                        if ($elseFound) {
                            throw new UnexpectedTokenException("The \"condition\" attribute must have only one else statement.");
                        }

                        $this->_elsePath = $element->getNodePath();

                        $elseFound = true;
                        break;
                }
            }
        } else {
            throw new ElementNotFoundException("The \"b:condition\" element must have at least one if statement.");
        }
    }

    /**
     * Render the token.
     *
     * @return \DOMNode|null
     */
    public function render(): ?\DOMNode
    {
        $truePath = null;

        foreach ($this->_conditionsMap as $path => $condition) {
            if ($condition instanceof ConditionAttribute) {
                $condition->parse();
                if ($condition->evaluate()) {
                    $truePath = $path;
                    break;
                }
            }
        }

        $domElement = null;
        $xPath = new \DOMXPath($this->_document);

        $resultPath = $truePath !== null ? $truePath : ($this->_elsePath !== null ? $this->_elsePath : null);

        if ($resultPath !== null) {
            $statement = $xPath->query($resultPath)->item(0);
            $domElement = $this->_document->createElement("b:outputWrapper", "");
            Utilities::appendHTML($domElement, Utilities::innerHTML($statement));
            // foreach ($statement->childNodes as $child) {
            //     $domElement->appendChild($child->cloneNode(true));
            // }
        }

        return $domElement;
    }
}
