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

namespace Bubble\Renderer;

use Bubble\Parser\Tokenizer;
use Bubble\Data\DataModel;
use Bubble\Data\DataResolver;
use Bubble\Util\Utilities;
use Bubble\Parser\IParser;
use Bubble\Tokens\ConditionToken;

/**
 * Template file
 *
 * Represents a template file parsed in
 * a set of tokens.
 *
 * @category Renderer
 * @package  Bubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  LGPL-3.0 <https://opensource.org/licenses/LGPL-3.0>
 * @link     http://bubble.na2axl.tk/docs/api/Bubble/Parser/Tokenizer
 */
class Template implements IParser, IRenderer
{
    private const SCHEMA_URI = "http://bubble.na2axl.tk/schema";

    public const DATA_MODEL_QUERY_REGEX = "#\\\$\\{([a-zA-Z0-9._()\\[\\]]+)\\}#U";

    public const EXPRESSION_REGEX = "#\\{\\{(.+)\\}\\}#U";

    private $_templateString;

    /**
     * The DOM document of this template.
     *
     * @var \DOMDocument
     */
    private $_dom;

    /**
     * Tokens list.
     *
     * @var TokensList
     */
    private $_tokensList;

    /**
     * Document xPath object.
     *
     * @var \DOMXPath
     */
    private $_xPath;

    /**
     * The data model to use when
     * rendering the template.
     *
     * @var DataModel
     */
    private $_dataModel;

    /**
     * The data resolver based on the
     * current data model.
     *
     * @var DataResolver
     */
    private $_dataResolver;

    /**
     * Checks if the template has
     * already been parsed.
     *
     * @var bool
     */
    private $_parsed = false;

    private function __construct(string $content)
    {
        $this->_templateString = $content;
    }

    public function setDataModel(DataModel $model)
    {
        $this->_dataModel = $model;
        $this->_dataResolver = new DataResolver($this->_dataModel);
    }

    public function parse()
    {
        if (!$this->_parsed) {
            $this->_preParse();
            $this->_populateData();
            $this->_postParse();
            $this->_parsed = true;
        }
    }

    public function render(): \DOMNode
    {
        $bubbleOutput = new \DOMDocument("1.0", "UTF-8");
        $bubbleOutput->formatOutput = true;
        $bubbleOutput->preserveWhiteSpace = true;

        $this->parse();

        foreach ($this->_dom->documentElement->childNodes as $child) {
            $bubbleOutput->appendChild($bubbleOutput->importNode($child, true));
        }

        $bubbleOutput->normalize();

        return $bubbleOutput;
    }

    public function outputString(): string
    {
        return $this->render()->saveHTML();
    }

    public function outputFile(string $path)
    {
        file_put_contents($path, $this->outputString());
    }

    private function _loadParser(Tokenizer $parser)
    {
        $this->_dom = $parser->getDom();
        $this->_tokensList = $parser->getTokens();
        $this->_tokensList->setTemplate($this);

        $this->_xPath = new \DOMXPath($this->_dom);
        $this->_xPath->registerNamespace("b", self::SCHEMA_URI);
    }

    private function _preParse()
    {
        $this->_loadParser(Tokenizer::tokenize($this->_templateString));

        $toReplace = array();
        $toDelete  = array();

        $found = false;

        foreach ($this->_tokensList as $k => $token) {
            if ($token->getType() === PRE_PARSE_TOKEN || $token->getType() === ALL_STATE_PARSE_TOKEN) {
                $token->parse();

                $found = $token->getType() === PRE_PARSE_TOKEN;

                $res = $token->render();
                if ($res === null) {
                    array_push($toDelete, $this->_xPath->query($token->getPath())->item(0));
                } else {
                    array_push($toReplace, array($res, $this->_xPath->query($token->getPath())->item(0)));
                }
            }
        }

        foreach ($toReplace as $replacement) {
            if ($replacement[0]->nodeName === "b:outputWrapper") {
                foreach ($replacement[0]->childNodes as $child) {
                    $replacement[1]->parentNode->insertBefore($child->cloneNode(true), $replacement[1]);
                }
                $replacement[1]->parentNode->removeChild($replacement[1]);
            } else {
                $replacement[1]->parentNode->replaceChild($replacement[0], $replacement[1]);
            }
        }

        foreach ($toDelete as $delete) {
            $delete->parentNode->removeChild($delete);
        }

        $this->_templateString = $this->_dom->saveXML();

        if ($found) {
            $this->_preParse();
        }
    }

    private function _postParse()
    {
        $this->_loadParser(Tokenizer::tokenize($this->_templateString));

        $toReplace = array();
        $toDelete  = array();

        $found = false;

        foreach ($this->_tokensList as $k => $token) {
            if ($token->getType() === POST_PARSE_TOKEN || $token->getType() === ALL_STATE_PARSE_TOKEN) {
                $token->parse();

                $found = $token->getType() == POST_PARSE_TOKEN;

                $res = $token->render();
                if ($res === null) {
                    array_push($toDelete, $this->_xPath->query($token->getPath())->item(0));
                } else {
                    array_push($toReplace, array($res, $this->_xPath->query($token->getPath())->item(0)));
                }
            }
        }

        foreach ($toReplace as $replacement) {
            if ($replacement[0]->nodeName === "b:outputWrapper") {
                foreach ($replacement[0]->childNodes as $child) {
                    $replacement[1]->parentNode->insertBefore($child->cloneNode(true), $replacement[1]);
                }
                $replacement[1]->parentNode->removeChild($replacement[1]);
            } else {
                $replacement[1]->parentNode->replaceChild($replacement[0], $replacement[1]);
            }
        }

        foreach ($toDelete as $delete) {
            $delete->parentNode->removeChild($delete);
        }

        $this->_templateString = $this->_dom->saveXML();

        if ($found) {
            $this->_postParse();
        }
    }

    private function _populateData()
    {
        $this->_templateString = preg_replace_callback(self::DATA_MODEL_QUERY_REGEX, function ($m) {
            return Utilities::toString($this->_dataResolver->resolve($m[1]));
        }, $this->_templateString);

        $this->_templateString = preg_replace_callback(self::EXPRESSION_REGEX, function ($m) {
            // TODO: Use EvalContext instead
            $res = eval("return {$m[1]};");
            return Utilities::toString($res);
        }, $this->_templateString);

        return $this->_templateString;
    }

    public static function fromFile(string $path): Template
    {
        if (file_exists($path)) {
            return self::fromString(file_get_contents($path));
        } else {
            throw new TemplateNotFoundException($path);
        }
    }

    public static function fromString(string $content): Template
    {
        return new Template($content);
    }

    /**
     * Get data resolver.
     *
     * @return  DataResolver
     */
    public function getResolver()
    {
        return $this->_dataResolver;
    }
}
