<?php

/**
 * Bubble - A PHP template engine
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
 * @package   Bubble
 * @author    Axel Nana <ax.lnana@outlook.com>
 * @copyright 2018 Aliens Group, Inc.
 * @license   MIT <https://github.com/na2axl/bubble/blob/master/LICENSE>
 * @version   GIT: 1.1.0
 * @link      http://bubble.na2axl.tk
 */

namespace Bubble;

use Bubble\Data\DataModel;
use Bubble\Renderer\Template;
use Bubble\Util\KeyValuePair;

/**
 * Define the type of token which
 * are rendered after the data model
 * parse process.
 */
define('POST_PARSE_TOKEN', 0);

/**
 * Define the type of token which
 * are rendered before the data model
 * parse process.
 */
define('PRE_PARSE_TOKEN', 1);

/**
 * Define the type of token which
 * are rendered without caring of
 * the state of the data model
 * parse process.
 */
define('ALL_STATE_PARSE_TOKEN', 2);

/**
 * Define the type of token which
 * are processed when the parser
 * retrieve inclusions.
 */
define('INCLUDE_STATE_TOKEN', 3);

/**
 * Define if this configuration of PHP
 * support mb_* functions.
 */
define('MBSTRING_AVAILABLE', function_exists('mb_get_info'));

/**
 * Bubble main class
 *
 * Manage the Bubble template engine, parse and render template files.
 *
 * @category MainClass
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/na2axl/bubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Bubble
 */
class Bubble
{
    /**
     * The template data bindings.
     *
     * @var DataModel
     */
    private $_dataModel;

    /**
     * Stores the Bubble configuration
     *
     * @var BubbleConfig
     */
    private static $_bubbleConfig;

    public static function setConfiguration(BubbleConfig $config)
    {
        self::$_bubbleConfig = $config;
    }

    /**
     * Gets the Bubble configuration.
     *
     * @return BubbleConfig
     */
    public static function getConfiguration()
    {
        if (self::$_bubbleConfig === null) {
            self::setConfiguration(new BubbleConfig());
        }

        return self::$_bubbleConfig;
    }

    public function __construct()
    {
        $this->_dataModel = new DataModel;
    }

    public function set(string $key, $value)
    {
        $this->_dataModel->add(new KeyValuePair($key, $value));
    }

    public function renderFile(string $path): string
    {
        $template = Template::fromFile($path);
        $template->setDataModel($this->_dataModel);
        return $template->outputString();
    }

    public function renderString(string $content): string
    {
        $template = Template::fromString($content);
        $template->setDataModel($this->_dataModel);
        return $template->outputString();
    }

    public function compileFile(string $path, string $output): void
    {
        $template = Template::fromFile($path);
        $template->setDataModel($this->_dataModel);
        $template->outputFile($output);
    }

    public function compileString(string $content, string $output): void
    {
        $template = Template::fromString($content);
        $template->setDataModel($this->_dataModel);
        $template->outputFile($output);
    }
}

