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
 * @version   1.4.0
 * @link      http://bubble.na2axl.tk
 */

namespace ElementaryFramework\AirBubble\Renderer;

use DOMDocument;
use DOMImplementation;
use DOMNode;
use DOMXPath;
use ElementaryFramework\AirBubble\AirBubble;
use ElementaryFramework\AirBubble\Data\DataModel;
use ElementaryFramework\AirBubble\Data\DataResolver;
use ElementaryFramework\AirBubble\Exception\TemplateNotFoundException;
use ElementaryFramework\AirBubble\Parser\IParser;
use ElementaryFramework\AirBubble\Parser\Tokenizer;
use ElementaryFramework\AirBubble\Parser\TokensList;
use ElementaryFramework\AirBubble\Util\NamespacesRegistry;
use ElementaryFramework\AirBubble\Util\OutputIndenter;
use ElementaryFramework\AirBubble\Util\TemplateExtender;
use ElementaryFramework\AirBubble\Util\Utilities;
use Exception;

/**
 * Template file
 *
 * Represents a template file parsed in
 * a set of tokens.
 *
 * @category Renderer
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Parser/Tokenizer
 */
class Template implements IParser, IRenderer
{
    public const DATA_MODEL_QUERY_CHARS_FILTER = "[a-zA-Z0-9,._\\(\\)\\[\\]'\"\/ ]";

    public const DATA_MODEL_QUERY_REGEX = "/\\\$\\{(" . self::DATA_MODEL_QUERY_CHARS_FILTER . "+)\\}/U";

    public const EXPRESSION_REGEX = "/\\{\\{(.+)\\}\\}/U";

    private $_templateString;

    /**
     * The DOM document of this template.
     *
     * @var DOMDocument
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
     * @var DOMXPath
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

    private function __construct(string $content, DataModel $model)
    {
        $this->setDataModel($model);
        try { $content = Utilities::processExpressions($content, $this->_dataResolver); }
        catch (Exception $e) { }
        $this->_templateString = $this->_mergeWithParent($content);
    }

    public function setDataModel(DataModel $model)
    {
        $this->_dataModel = $model;
        $this->_dataResolver = new DataResolver($this->_dataModel);
    }

    public function parse()
    {
        if (!$this->_parsed) {
            $this->_processInclusions();
            $this->_preParse();
            $this->_populateData();
            $this->_postParse();
            $this->_parsed = true;
        }
    }

    public function render(): DOMNode
    {
        $bubbleOutput = new DOMDocument("1.0", "UTF-8");

        $this->parse();

        if ($this->_dom->doctype) {
            $domImp = new DOMImplementation();
            $bubbleOutput->appendChild($domImp->createDocumentType($this->_dom->doctype->name, $this->_dom->doctype->publicId, $this->_dom->doctype->systemId));
        }

        foreach ($this->_dom->documentElement->childNodes as $child) {
            $bubbleOutput->appendChild($bubbleOutput->importNode($child, true));
        }

        return $bubbleOutput;
    }

    public function outputString(): string
    {
        $output = Utilities::computeOutputString($this->render());

        if (AirBubble::getConfiguration()->isIndentOutput()) {
            $indenter = new OutputIndenter();
            try {
                $output = $indenter->indent($output);
            } catch (Exception $e) {
            }
        }

        return $output;
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

        $this->_xPath = new DOMXPath($this->_dom);

        foreach (NamespacesRegistry::registry() as $ns => $uri) {
            $this->_xPath->registerNamespace(rtrim($ns, ':'), $uri);
        }
    }

    private function _processInclusions()
    {
        $this->_loadParser(Tokenizer::tokenize($this->_templateString, $this->_dataResolver));

        $toReplace = array();
        $toDelete = array();

        $highest = $this->_tokensList->getHighestPriorityForStage(INCLUDE_TOKEN_STAGE);
        $lowest = $this->_tokensList->getLowestPriorityForStage(INCLUDE_TOKEN_STAGE);

        for ($priority = $highest; $priority <= $lowest; $priority++) {
            $found = false;

            foreach ($this->_tokensList as $k => $token) {
                if ($token->getStage() === INCLUDE_TOKEN_STAGE && $token->getPriority() === $priority) {
                    $token->parse();

                    $found = true;

                    $res = $token->render();

                    if ($res === null) {
                        array_push($toDelete, $this->_xPath->query($token->getPath())->item(0));
                    } else {
                        array_push($toReplace, array($res, $this->_xPath->query($token->getPath())->item(0)));
                    }

                    break;
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

            if ($found) break;
        }

        $this->_templateString = $this->_dom->saveXML();

        if ($this->_tokensList->hasTokenWithStage(INCLUDE_TOKEN_STAGE)) {
            $this->_processInclusions();
        }
    }

    private function _preParse()
    {
        $this->_loadParser(Tokenizer::tokenize($this->_templateString, $this->_dataResolver));

        $toReplace = array();
        $toDelete = array();

        $highest = $this->_tokensList->getHighestPriorityForStage(PRE_PARSE_TOKEN_STAGE);
        $lowest = $this->_tokensList->getLowestPriorityForStage(PRE_PARSE_TOKEN_STAGE);

        for ($priority = $highest; $priority <= $lowest; $priority++) {
            $found = false;

            foreach ($this->_tokensList as $k => $token) {
                if (($token->getStage() === PRE_PARSE_TOKEN_STAGE || $token->getStage() === UNDEFINED_PARSE_TOKEN_STAGE) && $token->getPriority() === $priority) {
                    $token->parse();

                    $found = true;

                    $res = $token->render();

                    if ($res === null) {
                        array_push($toDelete, $this->_xPath->query($token->getPath())->item(0));
                    } else {
                        array_push($toReplace, array($res, $this->_xPath->query($token->getPath())->item(0)));
                    }

                    break;
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

            if ($found) break;
        }

        $this->_templateString = $this->_dom->saveXML();

        if ($this->_tokensList->hasTokenWithStage(PRE_PARSE_TOKEN_STAGE)) {
            $this->_preParse();
        }
    }

    private function _postParse()
    {
        $this->_loadParser(Tokenizer::tokenize($this->_templateString, $this->_dataResolver));

        $toReplace = array();
        $toDelete = array();


        $highest = $this->_tokensList->getHighestPriorityForStage(POST_PARSE_TOKEN_STAGE);
        $lowest = $this->_tokensList->getLowestPriorityForStage(POST_PARSE_TOKEN_STAGE);

        for ($priority = $highest; $priority <= $lowest; $priority++) {
            $found = false;

            foreach ($this->_tokensList as $k => $token) {
                if (($token->getStage() === POST_PARSE_TOKEN_STAGE || $token->getStage() === UNDEFINED_PARSE_TOKEN_STAGE) && $token->getPriority() === $priority) {
                    $token->parse();

                    $found = true;

                    $res = $token->render();
                    if ($res === null) {
                        array_push($toDelete, $this->_xPath->query($token->getPath())->item(0));
                    } else {
                        array_push($toReplace, array($res, $this->_xPath->query($token->getPath())->item(0)));
                    }

                    break;
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

            if ($found) break;
        }

        $this->_templateString = $this->_dom->saveXML();

        if ($this->_tokensList->hasTokenWithStage(POST_PARSE_TOKEN_STAGE)) {
            $this->_postParse();
        }
    }

    private function _populateData()
    {
        $this->_templateString = Utilities::populateData($this->_templateString, $this->_dataResolver);
        return $this->_templateString;
    }

    private function _mergeWithParent($content): string
    {
        if (TemplateExtender::isExtender($content)) {
            $parent = TemplateExtender::getParentTemplate($content, $this->_dataResolver);
            $content = $this->_mergeWithParent(TemplateExtender::merge($parent, $content));
        }

        return $content;
    }

    public static function fromFile(string $path, DataModel $model): Template
    {
        $templatePath = Utilities::resolveTemplate($path);

        if (file_exists($templatePath)) {
            return self::fromString(file_get_contents($templatePath), $model);
        } else {
            throw new TemplateNotFoundException($path);
        }
    }

    public static function fromString(string $content, DataModel $model): Template
    {
        return new Template($content, $model);
    }

    /**
     * Get data resolver.
     *
     * @return  DataResolver
     */
    public function getResolver(): DataResolver
    {
        return $this->_dataResolver;
    }

    /**
     * Returns a copy of the DataModel handled
     * by this Template.
     *
     * Any changes affected to the returned DataModel
     * will not affect this instance of Template, until
     * you call {@see Template::setDataModel} with the
     * modified DataModel.
     *
     * @return DataModel
     */
    public function getDataModel(): DataModel
    {
        return $this->_dataModel->copy();
    }
}
