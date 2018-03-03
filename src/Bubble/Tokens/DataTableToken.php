<?php

/**
 * Bubble - A PHP template engine
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category  Library
 * @package   Bubble
 * @author    Axel Nana <ax.lnana@outlook.com>
 * @copyright 2018 Aliens Group, Inc.
 * @license   LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
 * @version   GIT: 0.0.1
 * @link      http://bubble.na2axl.tk
 */

namespace Bubble\Tokens;

use Bubble\Exception\UnexpectedTokenException;
use Bubble\Util\Utilities;
use Bubble\Parser\AttributesList;
use Bubble\Attributes\ValueAttribute;
use Bubble\Attributes\VarAttribute;
use Bubble\Attributes\KeyAttribute;
use Bubble\Exception\ElementNotFoundException;
use Bubble\Renderer\Template;


/**
 * Foreach Token
 *
 * Parse and render foreach loops.
 *
 * @category Tokens
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Tokens/DataTableToken
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
                        throw new UnexpectedTokenException("The \"b:dataTable\" element can only have value, var or key for attributes.");
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
     */
    public function render(): ?\DOMNode
    {
        $iterator = null;
        $itemVar = null;
        $itemKey = null;

        foreach ($this->_attributes as $attr) {
            if ($attr instanceof ValueAttribute) {
                $iterator = $attr->getValue();
            } elseif ($attr instanceof VarAttribute) {
                $itemVar = $attr->getValue();
            } elseif ($attr instanceof KeyAttribute) {
                $itemKey = $attr->getValue();
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
            throw new \Exception("Non-iterable data provided to a foreach loop.");
        }

        $innerHTML = Utilities::innerHTML($this->_element);

        $domElement = $this->_document->createElement("table", "");

        $thead = $this->_document->createElement("thead", "");
        $thead->appendChild($this->_document->createElement("tr", ""));

        $tbody = $this->_document->createElement("tbody", "");

        $tfoot = $this->_document->createElement("tfoot", "");
        $tfoot->appendChild($this->_document->createElement("tr", ""));

        $headPassed = false;
        $footPassed = false;

        foreach ($data as $k => $v) {
            if (!$headPassed) {
                foreach ($this->_headers as $value) {
                    $value = preg_replace_callback(Template::DATA_MODEL_QUERY_REGEX, function (array $m) use ($itemVar, $itemKey, $iterator, $k, $v) {
                        return str_replace($itemVar, "{$iterator}[{$k}]", $m[0]);
                    }, $value);

                    if ($itemKey !== null) {
                        $value = preg_replace("#\\\$\\{{$itemKey}\\}#U", $k, $value);
                    }

                    Utilities::appendHTML(
                        $thead->getElementsByTagName("tr")->item(0),
                        "<th>{$value}</th>"
                    );
                }

                $headPassed = true;
            }

            $tbody->appendChild($this->_document->createElement("tr", ""));

            foreach ($this->_columns as $value) {
                $value = preg_replace_callback(Template::DATA_MODEL_QUERY_REGEX, function (array $m) use ($itemVar, $itemKey, $iterator, $k, $v) {
                    return str_replace($itemVar, "{$iterator}[{$k}]", $m[0]);
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
                        return str_replace($itemVar, "{$iterator}[{$k}]", $m[0]);
                    }, $value);

                    if ($itemKey !== null) {
                        $value = preg_replace("#\\\$\\{{$itemKey}\\}#U", $k, $value);
                    }

                    Utilities::appendHTML(
                        $tfoot->getElementsByTagName("tr")->item(0),
                        "<th>{$value}</th>"
                    );
                }

                $footPassed = true;
            }
        }

        $domElement->appendChild($thead);
        $domElement->appendChild($tbody);
        $domElement->appendChild($tfoot);

        return $domElement;
    }
}
