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
use ElementaryFramework\AirBubble\AirBubble;
use ElementaryFramework\AirBubble\Attributes\GenericAttribute;
use ElementaryFramework\AirBubble\Attributes\PathAttribute;
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
     * Token stage.
     */
    public const STAGE = INCLUDE_TOKEN_STAGE;

    /**
     * Token priority.
     */
    public const PRIORITY = HIGHEST_TOKEN_PRIORITY;

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
        $this->parse();

        $templatePath = null;
        $resolver = $this->_template->getResolver();
        $dataModel = $this->_template->getDataModel();

        foreach ($this->_attributes as $attr) {
            if ($attr instanceof PathAttribute) {
                $templatePath = Utilities::populateData($attr->getValue(), $resolver);
            } elseif ($attr instanceof GenericAttribute) {
                $data = Utilities::populateData($attr->getValue(), $resolver);
                $dataModel->set($attr->getName(), $data);
            }
        }

        $innerBubble = new AirBubble();
        $innerBubble->setDataModel($dataModel);

        $includeTemplate = $innerBubble->createTemplateFromFile($templatePath);
        $includeDOM = $includeTemplate->render();

        $includeString = Utilities::innerHTML($includeDOM);

        $domElement = $this->_document->createElement("b:fragment", "");

        Utilities::appendHTML($domElement, $includeString);

        return $domElement;
    }
}
