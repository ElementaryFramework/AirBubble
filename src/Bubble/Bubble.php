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
 * @license  LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
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

