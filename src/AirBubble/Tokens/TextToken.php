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

namespace AirBubble\Tokens;

use AirBubble\Attributes\ElementAttribute;
use AirBubble\Attributes\GenericAttribute;
use AirBubble\Attributes\ValueAttribute;
use AirBubble\Parser\AttributesList;

/**
 * Text Token
 *
 * Parse and render texts.
 *
 * @category Tokens
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Tokens/TextToken
 */
class TextToken extends BaseToken
{
    /**
     * Token name.
     */
    public const NAME = "text";

    /**
     * Token type.
     */
    public const TYPE = PRE_PARSE_TOKEN;

    protected function _parseAttributes()
    {
        if ($this->_element->hasAttributes()) {
            foreach ($this->_element->attributes as $attr) {
                switch ($attr->nodeName) {
                    case "element":
                        $this->_attributes->add(new ElementAttribute($attr, $this->_document));
                        break;

                    case "value":
                        $this->_attributes->add(new ValueAttribute($attr, $this->_document));
                        break;

                    default:
                        $this->_attributes->add(new GenericAttribute($attr, $this->_document));
                        break;
                }
            }
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
        $this->_attributes->parse();
    }

    /**
     * Render the token.
     *
     * @return \DOMNode
     */
    public function render(): \DOMNode
    {
        $attributesBuffer = array();
        $wrapper = null;
        $value = $this->_element->nodeValue;

        foreach ($this->_attributes as $attr) {
            if ($attr instanceof ElementAttribute) {
                $wrapper = $attr->getValue();
            } elseif ($attr instanceof ValueAttribute) {
                $value = $attr->getValue();
            } else {
                array_push($attributesBuffer, $attr);
            }
        }

        $domElement = null;

        if ($wrapper === null && count($attributesBuffer) === 0) {
            $domElement = $this->_document->createTextNode($value);
        } else {
            $wrapper = $wrapper === null ? "span" : $wrapper;
            $domElement = $this->_document->createElement($wrapper, $value);

            if (count($attributesBuffer) > 0) {
                foreach ($attributesBuffer as $attr) {
                    $attribute = $attr->render();
                    $domElement->appendChild($attribute);
                }
            }
        }

        return $domElement;
    }
}
