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
use ElementaryFramework\AirBubble\Attributes\FromAttribute;
use ElementaryFramework\AirBubble\Attributes\ToAttribute;
use ElementaryFramework\AirBubble\Attributes\VarAttribute;
use ElementaryFramework\AirBubble\Exception\ElementNotFoundException;
use ElementaryFramework\AirBubble\Exception\UnexpectedTokenException;
use ElementaryFramework\AirBubble\Util\Utilities;

/**
 * For Token
 *
 * Parse and render for loops.
 *
 * @category Tokens
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Tokens/ForToken
 */
class ForToken extends BaseToken
{
    /**
     * Token name.
     */
    public const NAME = "for";

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
     */
    public function render(): ?DOMNode
    {
        $itemEnd = null;
        $itemVar = null;
        $itemStart = null;
        $resolver = $this->_template->getResolver();

        foreach ($this->_attributes as $attr) {
            if ($attr instanceof FromAttribute) {
                $itemStart = intval(Utilities::evaluate($attr->getValue(), $resolver));
            } elseif ($attr instanceof VarAttribute) {
                $itemVar = $attr->getValue();
            } elseif ($attr instanceof ToAttribute) {
                $itemEnd = intval(Utilities::evaluate($attr->getValue(), $resolver));
            }
        }

        if ($itemVar === null) {
            throw new ElementNotFoundException("The \"" . VarAttribute::NAME . "\" attribute is required in for loop.");
        }

        if ($itemStart === null) {
            throw new ElementNotFoundException("The \"" . FromAttribute::NAME . "\" attribute is required in for loop.");
        }

        if ($itemEnd === null) {
            throw new ElementNotFoundException("The \"" . ToAttribute::NAME . "\" attribute is required in for loop.");
        }

        $innerHTML = Utilities::innerHTML($this->_element);

        $domElement = $this->_document->createElement("b:outputWrapper", "");

        if ($itemStart < $itemEnd) {
            for ($i = $itemStart; $i <= $itemEnd; $i++) {
                Utilities::appendHTML($domElement, preg_replace("/\\\$\\{{$itemVar}\\}/U", $i, $innerHTML));
            }
        } else {
            for ($i = $itemStart; $i >= $itemEnd; $i--) {
                Utilities::appendHTML($domElement, preg_replace("/\\\$\\{{$itemVar}\\}/U", $i, $innerHTML));
            }
        }

        return $domElement;
    }

    /**
     * @inheritdoc
     *
     * @throws UnexpectedTokenException
     */
    protected function _parseAttributes()
    {
        if ($this->_element->hasAttributes()) {
            foreach ($this->_element->attributes as $attr) {
                switch ($attr->nodeName) {
                    case FromAttribute::NAME:
                        $this->_attributes->add(new FromAttribute($attr, $this->_document));
                        break;

                    case VarAttribute::NAME:
                        $this->_attributes->add(new VarAttribute($attr, $this->_document));
                        break;

                    case ToAttribute::NAME:
                        $this->_attributes->add(new ToAttribute($attr, $this->_document));
                        break;

                    default:
                        throw new UnexpectedTokenException(
                            "The \"b:for\" loop can only have \"" . VarAttribute::NAME . "\", \"" . FromAttribute::NAME . "\" or \"" . ToAttribute::NAME . "\" for attributes."
                        );
                }
            }
        }
    }
}
