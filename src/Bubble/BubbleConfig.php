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
     * Get the template base path.
     *
     * @return  string
     */
    public function getTemplatesBasePath()
    {
        return $this->_templatesBasePath;
    }

    /**
     * Set the template base path.
     *
     * @param  string  $_templatesBasePath  will be resolved.
     *
     * @return  self
     */
    public function setTemplatesBasePath(string $path)
    {
        $this->_templatesBasePath = $path;
        return $this;
    }
}