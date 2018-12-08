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

use ElementaryFramework\AirBubble\AirBubble;
use ElementaryFramework\AirBubble\Attributes\GenericAttribute;
use ElementaryFramework\AirBubble\Attributes\PathAttribute;
use ElementaryFramework\AirBubble\Renderer\Template;
use ElementaryFramework\AirBubble\Util\Utilities;

/**
 * Include Token
 *
 * Allow templates to include another ones.
 *
 * @category Tokens
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Tokens/IncludeToken
 */
class IncludeToken extends BaseToken
{
    /**
     * Token name.
     */
    public const NAME = "include";

    /**
     * Token type.
     */
    public const TYPE = INCLUDE_STATE_TOKEN;

    /**
     * @inheritdoc
     */
    protected function _parseAttributes()
    {
        if ($this->_element->hasAttributes()) {
            foreach ($this->_element->attributes as $attr) {
                switch ($attr->nodeName) {
                    case PathAttribute::NAME:
                        $this->_attributes->add(new PathAttribute($attr, $this->_document));
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
     * @throws \ElementaryFramework\AirBubble\Exception\InvalidQueryException
     * @throws \ElementaryFramework\AirBubble\Exception\KeyNotFoundException
     * @throws \ElementaryFramework\AirBubble\Exception\PropertyNotFoundException
     */
    public function render(): ?\DOMNode
    {
        $this->parse();

        $templatePath = null;
        $dataContext = array();
        $resolver = $this->_template->getResolver();

        foreach ($this->_attributes as $attr) {
            if ($attr instanceof PathAttribute) {
                $templatePath = Utilities::evaluate($attr->getValue(), $resolver);
            } elseif ($attr instanceof GenericAttribute) {
                array_push($dataContext, $attr);
            }
        }

        $innerBubble = new AirBubble();

        foreach ($dataContext as $var) {
            $data = Utilities::evaluate($var->getValue(), $resolver);
            $innerBubble->set($var->getName(), $data);
        }

        $includeTemplate = $innerBubble->createTemplateFromFile($templatePath);
        $includeDOM = $includeTemplate->render();

        $includeString = $includeDOM->saveXML($includeDOM->documentElement);

        $domElement = $this->_document->createElement("b:outputWrapper", "");

        Utilities::appendHTML($domElement, $includeString);

        return $domElement;
    }
}