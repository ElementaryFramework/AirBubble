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

namespace ElementaryFramework\AirBubble\Tokens;

use ElementaryFramework\AirBubble\Attributes\ConditionAttribute;
use ElementaryFramework\AirBubble\Exception\ElementNotFoundException;
use ElementaryFramework\AirBubble\Exception\UnexpectedTokenException;
use ElementaryFramework\AirBubble\Parser\AttributesList;
use ElementaryFramework\AirBubble\Util\Utilities;

/**
 * Condition Token
 *
 * Parse and render conditions.
 *
 * @category Tokens
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Tokens/ConditionToken
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

    /**
     * @inheritdoc
     *
     * @throws UnexpectedTokenException When the element has attributes.
     */
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
     *
     * @throws ElementNotFoundException When the parser was usable to find a statement.
     * @throws UnexpectedTokenException When the parser found a token on a bad place.
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
        }

        return $domElement;
    }
}
