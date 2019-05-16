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

use DOMNode;
use ElementaryFramework\AirBubble\Attributes\ElementAttribute;
use ElementaryFramework\AirBubble\Attributes\GenericAttribute;
use ElementaryFramework\AirBubble\Attributes\ValueAttribute;

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
     * Token stage.
     */
    public const STAGE = PRE_PARSE_TOKEN_STAGE;

    /**
     * Token priority.
     */
    public const PRIORITY = 2;

    /**
     * @inheritDoc
     */
    protected function _parseAttributes()
    {
        if ($this->_element->hasAttributes()) {
            foreach ($this->_element->attributes as $attr) {
                switch ($attr->nodeName) {
                    case ElementAttribute::NAME:
                        $this->_attributes->add(new ElementAttribute($attr, $this->_document));
                        break;

                    case ValueAttribute::NAME:
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
    public function getStage(): int
    {
        return self::STAGE;
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
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return self::PRIORITY;
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
     * @return DOMNode|null
     */
    public function render(): ?DOMNode
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
