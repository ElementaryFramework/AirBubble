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
use ElementaryFramework\AirBubble\Attributes\GenericAttribute;
use ElementaryFramework\AirBubble\Attributes\ItemsAttribute;
use ElementaryFramework\AirBubble\Attributes\KeyAttribute;
use ElementaryFramework\AirBubble\Attributes\LabelAttribute;
use ElementaryFramework\AirBubble\Attributes\SelectedAttribute;
use ElementaryFramework\AirBubble\Attributes\ValueAttribute;
use ElementaryFramework\AirBubble\Attributes\VarAttribute;
use ElementaryFramework\AirBubble\Exception\ElementNotFoundException;
use ElementaryFramework\AirBubble\Exception\InvalidDataException;
use ElementaryFramework\AirBubble\Exception\InvalidQueryException;
use ElementaryFramework\AirBubble\Exception\KeyNotFoundException;
use ElementaryFramework\AirBubble\Exception\PropertyNotFoundException;
use ElementaryFramework\AirBubble\Renderer\Template;
use ElementaryFramework\AirBubble\Util\SelectItemsList;
use ElementaryFramework\AirBubble\Util\Utilities;

/**
 * Select Items Token
 *
 * Parse and render HTML select dropbox.
 *
 * @category Tokens
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Tokens/SelectItemsToken
 */
class SelectItemsToken extends BaseToken
{
    /**
     * Token name.
     */
    public const NAME = "selectItems";

    /**
     * Token stage.
     */
    public const STAGE = PRE_PARSE_TOKEN_STAGE;

    /**
     * Token priority.
     */
    public const PRIORITY = 2;

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
     *
     * @throws ElementNotFoundException
     * @throws InvalidDataException
     * @throws InvalidQueryException
     * @throws KeyNotFoundException
     * @throws PropertyNotFoundException
     */
    public function render(): ?DOMNode
    {
        $attributesBuffer = array();

        $iterator = null;
        $itemVar = null;
        $itemKey = null;
        $itemValue = null;
        $itemLabel = null;
        $itemSelected = null;

        foreach ($this->_attributes as $attr) {
            if ($attr instanceof ItemsAttribute) {
                $iterator = $attr->getValue();
            } elseif ($attr instanceof VarAttribute) {
                $itemVar = $attr->getValue();
            } elseif ($attr instanceof KeyAttribute) {
                $itemKey = $attr->getValue();
            } elseif ($attr instanceof ValueAttribute) {
                $itemValue = $attr->getValue();
            } elseif ($attr instanceof LabelAttribute) {
                $itemLabel = $attr->getValue();
            } elseif ($attr instanceof SelectedAttribute) {
                $itemSelected = $attr->getValue();
            } else {
                array_push($attributesBuffer, $attr);
            }
        }

        if ($iterator === null) {
            throw new ElementNotFoundException("The \"" . ItemsAttribute::NAME . "\" attribute is required in \"b:selectItems\".");
        }

        $iterator = preg_replace(Template::DATA_MODEL_QUERY_REGEX, "$1", $iterator);
        $data = $this->_template->getResolver()->resolve($iterator);

        if (!is_iterable($data)) {
            throw new InvalidDataException("Non-iterable data provided to a selectItems token.");
        }

        if ($data instanceof SelectItemsList) {
            $itemValue = "\${item.getKey()}";
            $itemLabel = "\${item.getValue()}";
            $itemVar = "item";
        }

        if ($itemValue === null) {
            throw new ElementNotFoundException("The \"" . ValueAttribute::NAME . "\" attribute is required in \"b:selectItems\".");
        }

        if ($itemLabel === null) {
            throw new ElementNotFoundException("The \"" . LabelAttribute::NAME . "\" attribute is required in \"b:selectItems\".");
        }

        if ($itemVar === null) {
            throw new ElementNotFoundException("The \"" . VarAttribute::NAME . "\" attribute is required in \"b:selectItems\".");
        }

        if ($itemSelected !== null) {
            try {
                $itemSelected = $this->_template->getResolver()->resolve($itemSelected);
            } catch (InvalidQueryException $_) {
            }

            $innerHTML = "<option value='{$itemValue}' {{ {$itemValue} === {$itemSelected} ? 'selected=\"true\"' : '' }}>{$itemLabel}</option>";
        } else {
            $innerHTML = "<option value='{$itemValue}'>{$itemLabel}</option>";
        }

        $domElement = $this->_document->createElement("select", "");

        $charsFilter = Template::DATA_MODEL_QUERY_CHARS_FILTER;

        foreach ($data as $k => $v) {
            $iterationHTML = preg_replace_callback(
                Template::DATA_MODEL_QUERY_REGEX,
                function (array $m) use ($itemVar, $itemValue, $iterator, $k, $v, $charsFilter) {
                    return preg_replace("#^\\\$\\{({$charsFilter}*){$itemVar}\b#U", "\${\$1{$iterator}[{$k}]", $m[0]);
                },
                $innerHTML
            );

            if ($itemKey !== null) {
                $iterationHTML = preg_replace("/\\\$\\{{$itemKey}\\}/U", $k, $iterationHTML);
            }

            $iterationHTML = Utilities::processExpressions($iterationHTML, $this->_template->getResolver());

            Utilities::appendHTML($domElement, $iterationHTML);
        }

        if (count($attributesBuffer) > 0) {
            foreach ($attributesBuffer as $attr) {
                $attribute = $attr->render();
                $domElement->appendChild($attribute);
            }
        }

        return $domElement;
    }

    /**
     * @inheritdoc
     */
    protected function _parseAttributes()
    {
        if ($this->_element->hasAttributes()) {
            foreach ($this->_element->attributes as $attr) {
                switch ($attr->nodeName) {
                    case ItemsAttribute::NAME:
                        $this->_attributes->add(new ItemsAttribute($attr, $this->_document));
                        break;

                    case VarAttribute::NAME:
                        $this->_attributes->add(new VarAttribute($attr, $this->_document));
                        break;

                    case KeyAttribute::NAME:
                        $this->_attributes->add(new KeyAttribute($attr, $this->_document));
                        break;

                    case ValueAttribute::NAME:
                        $this->_attributes->add(new ValueAttribute($attr, $this->_document));
                        break;

                    case LabelAttribute::NAME:
                        $this->_attributes->add(new LabelAttribute($attr, $this->_document));
                        break;

                    case SelectedAttribute::NAME:
                        $this->_attributes->add(new SelectedAttribute($attr, $this->_document));
                        break;

                    default:
                        $this->_attributes->add(new GenericAttribute($attr, $this->_document));
                        break;
                }
            }
        }
    }
}
