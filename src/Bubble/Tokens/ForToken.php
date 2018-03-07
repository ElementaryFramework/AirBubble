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

use Bubble\Attributes\FromAttribute;
use Bubble\Attributes\ToAttribute;
use Bubble\Attributes\ValueAttribute;
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
 * @license  LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
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
            throw new ElementNotFoundException("The \"" . ValueAttribute::NAME . "\" attribute is required in foreach loop.");
        }

        if ($itemStart === null) {
            throw new ElementNotFoundException("The \"" . FromAttribute::NAME . "\" attribute is required in foreach loop.");
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
