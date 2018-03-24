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

namespace Bubble;

/**
 * Bubble configuration class
 *
 * Defines configurations values to use when rendering Bubble templates.
 *
 * @category MainClass
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/BubbleConfig
 */
class BubbleConfig
{
    /**
     * The base path in which all relative path to templates
     * will be resolved.
     *
     * @var string
     */
    private $_templatesBasePath;

    /**
     * Configure the template encoding to use.
     *
     * @var string
     */
    private $_templateEncoding;

    /**
     * Define if the renderer have to indent
     * the output.
     *
     * @var bool
     */
    private $_indentOutput;

    /**
     * Checks if the renderer have to indent the
     * output.
     *
     * @return bool
     */
    public function isIndentOutput(): bool
    {
        return $this->_indentOutput;
    }

    /**
     * Sets if the renderer have to
     * indent the output.
     *
     * @param bool $indentOutput
     */
    public function setIndentOutput(bool $indentOutput): void
    {
        $this->_indentOutput = $indentOutput;
    }

    /**
     * Get the template base path.
     *
     * @return string
     */
    public function getTemplatesBasePath()
    {
        return $this->_templatesBasePath;
    }

    /**
     * Set the template base path.
     *
     * @param string $path The path.
     *
     * @return self
     */
    public function setTemplatesBasePath(string $path)
    {
        $this->_templatesBasePath = $path;
        return $this;
    }

    /**
     * Get the template encoding to use.
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->_templateEncoding;
    }

    /**
     * Set the template encoding to use.
     *
     * @param string $encoding The template encoding to use.
     *
     * @return self
     */
    public function setEncoding(string $encoding)
    {
        $this->_templateEncoding = $encoding;
        return $this;
    }
}