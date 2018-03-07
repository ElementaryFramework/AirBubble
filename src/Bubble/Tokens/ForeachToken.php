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

use Bubble\Parser\AttributesList;
use Bubble\Attributes\ElementAttribute;
use Bubble\Attributes\ValueAttribute;
use Bubble\Attributes\GenericAttribute;
use Bubble\Attributes\ConditionAttribute;
use Bubble\Exception\UnexpectedTokenException;
use Bubble\Exception\ElementNotFoundException;
use Bubble\Attributes\VarAttribute;
use Bubble\Attributes\KeyAttribute;
use Bubble\Renderer\Template;
use Bubble\Util\Utilities;
use Bubble\Exception\InvalidDataException;

/**
 * Foreach Token
 *
 * Parse and render foreach loops.
 *
 * @category Tokens
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Tokens/ForeachToken
 */
class ForeachToken extends BaseToken
{
    /**
     * Token name.
     */
    public const NAME = "foreach";

    /**
     * Token type.
     */
    public const TYPE = PRE_PARSE_TOKEN;

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
                        throw new UnexpectedTokenException(
                            "The \"b:foreach\" loop can only have \"value\", \"var\" or \"key\" for attributes."
                        );
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
            throw new ElementNotFoundException("The \"var\" attribute is required in foreach loop.");
        }

        if ($iterator === null) {
            throw new ElementNotFoundException("The \"value\" attribute is required in foreach loop.");
        }

        $iterator = preg_replace(Template::DATA_MODEL_QUERY_REGEX, "$1", $iterator);
        $data = $this->_template->getResolver()->resolve($iterator);

        if (!is_iterable($data)) {
            throw new InvalidDataException("Non-iterable data provided to a foreach loop.");
        }

        $innerHTML = Utilities::innerHTML($this->_element);

        $domElement = $this->_document->createElement("b:outputWrapper", "");

        foreach ($data as $k => $v) {
            $iterationHTML = preg_replace_callback(
                Template::DATA_MODEL_QUERY_REGEX,
                function (array $m) use ($itemVar, $itemKey, $iterator, $k, $v) {
                    return str_replace($itemVar, "{$iterator}[{$k}]", $m[0]);
                },
                $innerHTML
            );

            if ($itemKey !== null) {
                $iterationHTML = preg_replace("#\\\$\\{{$itemKey}\\}#U", $k, $iterationHTML);
            }

            Utilities::appendHTML($domElement, $iterationHTML);
        }

        return $domElement;
    }
}
