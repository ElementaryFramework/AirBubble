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

namespace AirBubble;

/**
 * AirBubble configuration class
 *
 * Defines configurations values to use when rendering AirBubble templates.
 *
 * @category MainClass
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/BubbleConfig
 */
class AirBubbleConfig
{
    /**
     * The base path in which all relative path to templates
     * will be resolved.
     *
     * @var string
     */
    private $_templatesBasePath = "./";

    /**
     * Configure the template encoding to use.
     *
     * @var string
     */
    private $_templateEncoding = "utf-8";

    /**
     * Define if the renderer have to indent
     * the output.
     *
     * @var bool
     */
    private $_indentOutput = true;

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