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

use ElementaryFramework\AirBubble\Attributes\VarAttribute;
use ElementaryFramework\AirBubble\Attributes\ValueAttribute;

use ElementaryFramework\AirBubble\Exception\ParseErrorException;
use ElementaryFramework\AirBubble\Exception\UnexpectedTokenException;

use ElementaryFramework\AirBubble\Util\Utilities;

/**
 * Assign Token
 *
 * Allow to create a data model variable or assign a new value to an existing one directly in the template.
 *
 * @category Tokens
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Tokens/AssignToken
 */
class AssignToken extends BaseToken
{
    /**
     * Token name.
     *
     * @var string
     */
    public const NAME = "assign";

    /**
     * Token type.
     *
     * @var int
     */
    public const TYPE = PRE_PARSE_TOKEN;

    /**
     * @inheritdoc
     */
    protected function _parseAttributes()
    {
        if ($this->_element->hasAttributes()) {
            foreach ($this->_element->attributes as $attr) {
                switch ($attr->nodeName) {
                    case VarAttribute::NAME:
                        $this->_attributes->add(new VarAttribute($attr, $this->_document));
                        break;

                    case ValueAttribute::NAME:
                        $this->_attributes->add(new ValueAttribute($attr, $this->_document));
                        break;

                    default:
                        throw new UnexpectedTokenException("The attribute \"{$attr->nodename}\" is not supported for this tag.");
                }
            }
        }
    }

    /**
     * Gets the type of this token.
     *
     * @return int
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
     */
    public function render(): ?\DOMNode
    {
        $var = $value = null;

        $resolver = $this->_template->getResolver();

        foreach ($this->_attributes as $attr) {
            if ($attr instanceof VarAttribute) {
                $var = $attr->getValue();
            } elseif ($attr instanceof ValueAttribute) {
                $value = Utilities::evaluate($attr->getValue(), $resolver);
            }
        }

        if (null === $var) {
            throw new ParseErrorException("The attribute \"var\" is required for this tag.");
        }

        if (null === $value) {
            throw new ParseErrorException("The attribute \"value\" is required for this tag.");
        }

        $dataModel = $this->_template->getDataModel();
        $dataModel->set($var, $value);
        $this->_template->setDataModel($dataModel);

        return null;
    }
}
