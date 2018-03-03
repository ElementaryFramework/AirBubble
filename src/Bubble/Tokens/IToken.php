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

use Bubble\Parser\IParser;
use Bubble\Renderer\IRenderer;
use Bubble\Parser\AttributesList;
use Bubble\Renderer\Template;

/**
 * Token interface
 *
 * Provide methods and properties of all Bubble tokens.
 *
 * @category Tokens
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Tokens/IToken
 */
interface IToken extends IParser, IRenderer
{
    /**
     * Gets the type of the token.
     *
     * A token can take only two kind of
     * type: PRE_PARSE_TOKEN and POST_PARSE_TOKEN.
     *
     * @return integer
     */
    public function getType(): int;

    /**
     * Gets the token's name.
     *
     * @return string The token's name
     */
    public function getName(): string;

    /**
     * Gets the XPath to this token.
     *
     * @return string The XPath
     */
    public function getPath(): string;

    /**
     * Gets the list of attributes in this token.
     *
     * @return AttributesList
     */
    public function getAttributes(): AttributesList;

    public function setTemplate(Template &$template);
}
