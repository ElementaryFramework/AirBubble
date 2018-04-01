<?php

/**
 * Bubble - A PHP template engine
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
 * @package   Bubble
 * @author    Axel Nana <ax.lnana@outlook.com>
 * @copyright 2018 Aliens Group, Inc.
 * @license   MIT <https://github.com/na2axl/bubble/blob/master/LICENSE>
 * @version   GIT: 1.1.0
 * @link      http://bubble.na2axl.tk
 */

namespace Bubble\Tokens;

use Bubble\Attributes\FromAttribute;
use Bubble\Attributes\ToAttribute;
use Bubble\Attributes\VarAttribute;
use Bubble\Exception\ElementNotFoundException;
use Bubble\Exception\UnexpectedTokenException;
use Bubble\Parser\AttributesList;
use Bubble\Util\Utilities;

/**
 * For Token
 *
 * Parse and render for loops.
 *
 * @category Tokens
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/na2axl/bubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Tokens/ForToken
 */
class ForToken extends BaseToken
{
    /**
     * Token name.
     */
    public const NAME = "for";

    /**
     * Token type.
     */
    public const TYPE = PRE_PARSE_TOKEN;

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
     * @return \DOMNode|null
     *
     * @throws ElementNotFoundException
     */
    public function render(): ?\DOMNode
    {
        $itemEnd = null;
        $itemVar = null;
        $itemStart = null;

        foreach ($this->_attributes as $attr) {
            if ($attr instanceof FromAttribute) {
                $itemStart = intval($attr->getValue());
            } elseif ($attr instanceof VarAttribute) {
                $itemVar = $attr->getValue();
            } elseif ($attr instanceof ToAttribute) {
                $itemEnd = intval($attr->getValue());
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
                            "The \"b:for\" loop can only have \"var\", \"from\" or \"to\" for attributes."
                        );
                }
            }
        }
    }
}
