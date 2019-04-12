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

use ElementaryFramework\AirBubble\Attributes\GenericAttribute;
use ElementaryFramework\AirBubble\Attributes\KeyAttribute;
use ElementaryFramework\AirBubble\Attributes\ValueAttribute;
use ElementaryFramework\AirBubble\Attributes\VarAttribute;
use ElementaryFramework\AirBubble\Exception\ElementNotFoundException;
use ElementaryFramework\AirBubble\Exception\UnexpectedTokenException;
use ElementaryFramework\AirBubble\Renderer\Template;
use ElementaryFramework\AirBubble\Util\Utilities;


/**
 * Foreach Token
 *
 * Parse and render foreach loops.
 *
 * @category Tokens
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Tokens/DataTableToken
 */
class DataTableToken extends BaseToken
{
    /**
     * Token name.
     */
    public const NAME = "dataTable";

    /**
     * Token type.
     */
    public const TYPE = PRE_PARSE_TOKEN;

    private $_headers = array();

    /**
     * Table columns content.
     *
     * @var array
     */
    private $_columns = array();

    private $_footers = array();

    protected function _parseAttributes()
    {
        if ($this->_element->hasAttributes()) {
            foreach ($this->_element->attributes as $attr) {
                switch ($attr->nodeName) {
                    case "key":
                        $this->_attributes->add(new KeyAttribute($attr, $this->_document));
                        break;

                    case "var":
                        $this->_attributes->add(new VarAttribute($attr, $this->_document));
                        break;

                    case "value":
                        $this->_attributes->add(new ValueAttribute($attr, $this->_document));
                        break;

                    default:
                        $this->_attributes->add(new GenericAttribute($attr, $this->_document));
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
     * @throws ElementNotFoundException
     * @throws UnexpectedTokenException
     */
    public function parse()
    {
        if (!$this->_element->hasChildNodes()) {
            throw new ElementNotFoundException("The \"b:condition\" element must have at least one if statement.");
        }

        $this->_attributes->parse();

        foreach ($this->_element->childNodes as $element) {
            if ($element->nodeName === "#text") {
                continue;
            }

            if ($element->nodeName !== "column") {
                throw new UnexpectedTokenException("Only \"column\" element is accepted as child of \"b:dataTable\".");
            }

            if ($element->hasChildNodes()) {
                $head = "";
                $body = "";
                $foot = "";

                foreach ($element->childNodes as $e) {
                    switch ($e->nodeName) {
                        case "#text":
                            continue;

                        case "head":
                            $head = Utilities::innerHTML($e);
                            break;

                        case "content":
                            $body = Utilities::innerHTML($e);
                            break;

                        case "foot":
                            $foot = Utilities::innerHTML($e);
                            break;

                        default:
                            throw new UnexpectedTokenException("The \"b:dataTable\" column element can only have for child a \"content\", and optionally an \"head\" and a \"foot\"");
                    }
                }

                $this->_headers[] = $head;
                $this->_columns[] = $body;
                $this->_footers[] = $foot;
            }
        }
    }

    /**
     * Render the token.
     *
     * @return \DOMNode|null
     *
     * @throws ElementNotFoundException
     * @throws \ElementaryFramework\AirBubble\Exception\InvalidQueryException
     * @throws \ElementaryFramework\AirBubble\Exception\KeyNotFoundException
     * @throws \ElementaryFramework\AirBubble\Exception\PropertyNotFoundException
     * @throws \Exception
     */
    public function render(): ?\DOMNode
    {
        $iterator = null;
        $itemVar = null;
        $itemKey = null;

        $domElement = $this->_document->createElement("table", "");

        foreach ($this->_attributes as $attr) {
            if ($attr instanceof ValueAttribute) {
                $iterator = $attr->getValue();
            } elseif ($attr instanceof VarAttribute) {
                $itemVar = $attr->getValue();
            } elseif ($attr instanceof KeyAttribute) {
                $itemKey = $attr->getValue();
            } else {
                $domElement->setAttribute($attr->getName(), $attr->getValue());
            }
        }

        if ($itemVar === null) {
            throw new ElementNotFoundException("The var attribute is required in dataTable.");
        }

        if ($iterator === null) {
            throw new ElementNotFoundException("The value attribute is required in dataTable.");
        }

        $iterator = preg_replace(Template::DATA_MODEL_QUERY_REGEX, "$1", $iterator);
        $data = $this->_template->getResolver()->resolve($iterator);

        if (!is_iterable($data)) {
            // TODO: Create an exception for this
            throw new \Exception("Non-iterable data provided to the data table.");
        }

        $thead = $this->_document->createElement("thead", "");
        $thead->appendChild($this->_document->createElement("tr", ""));

        $tbody = $this->_document->createElement("tbody", "");

        $tfoot = $this->_document->createElement("tfoot", "");
        $tfoot->appendChild($this->_document->createElement("tr", ""));

        $headPassed = false;
        $footPassed = false;

        $skipHead = true;
        $skipFoot = true;

        foreach ($data as $k => $v) {
            if (!$headPassed) {
                foreach ($this->_headers as $value) {
                    $value = preg_replace_callback(Template::DATA_MODEL_QUERY_REGEX, function (array $m) use ($itemVar, $itemKey, $iterator, $k, $v) {
                        return preg_replace("#^\\\$\\{{$itemVar}\b#U", "\${{$iterator}[{$k}]", $m[0]);
                    }, $value);

                    if ($itemKey !== null) {
                        $value = preg_replace("#\\\$\\{{$itemKey}\\}#U", $k, $value);
                    }

                    Utilities::appendHTML(
                        $thead->getElementsByTagName("tr")->item(0),
                        "<th>{$value}</th>"
                    );

                    if ($skipHead && strlen($value) > 0) {
                        $skipHead = false;
                    }
                }

                $headPassed = true;
            }

            $tbody->appendChild($this->_document->createElement("tr", ""));

            foreach ($this->_columns as $value) {
                $value = preg_replace_callback(Template::DATA_MODEL_QUERY_REGEX, function (array $m) use ($itemVar, $itemKey, $iterator, $k, $v) {
                    return preg_replace("#^\\\$\\{{$itemVar}\b#U", "\${{$iterator}[{$k}]", $m[0]);
                }, $value);

                if ($itemKey !== null) {
                    $value = preg_replace("#\\\$\\{{$itemKey}\\}#U", $k, $value);
                }

                Utilities::appendHTML(
                    $tbody->getElementsByTagName("tr")->item($k),
                    "<td>{$value}</td>"
                );
            }

            if (!$footPassed) {
                foreach ($this->_footers as $value) {
                    $value = preg_replace_callback(Template::DATA_MODEL_QUERY_REGEX, function (array $m) use ($itemVar, $itemKey, $iterator, $k, $v) {
                        return preg_replace("#^\\\$\\{{$itemVar}\b#U", "\${{$iterator}[{$k}]", $m[0]);
                    }, $value);

                    if ($itemKey !== null) {
                        $value = preg_replace("#\\\$\\{{$itemKey}\\}#U", $k, $value);
                    }

                    Utilities::appendHTML(
                        $tfoot->getElementsByTagName("tr")->item(0),
                        "<th>{$value}</th>"
                    );

                    if ($skipFoot && strlen($value) > 0) {
                        $skipFoot = false;
                    }
                }

                $footPassed = true;
            }
        }

        if (!$skipHead) {
            $domElement->appendChild($thead);
        }

        $domElement->appendChild($tbody);

        if (!$skipFoot) {
            $domElement->appendChild($tfoot);
        }

        return $domElement;
    }
}
