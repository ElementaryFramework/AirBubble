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

namespace ElementaryFramework\AirBubble\Directives;

use ElementaryFramework\AirBubble\Exception\ParseErrorException;
use ElementaryFramework\AirBubble\Exception\TemplateException;
use ElementaryFramework\AirBubble\Renderer\Template;
use ElementaryFramework\AirBubble\Util\NamespacesRegistry;
use ElementaryFramework\AirBubble\Util\Utilities;

/**
 * For Directive
 *
 * Represent the <b>for</b> directive.
 *
 * @category Attributes
 * @package  AirBubble
 * @author   Axel Nana <ax.lnana@outlook.com>
 * @license  MIT <https://github.com/ElementaryFramework/AirBubble/blob/master/LICENSE>
 * @link     http://bubble.na2axl.tk/docs/api/AirBubble/Attributes/ForDirective
 */
class ForDirective extends BaseDirective
{
    /**
     * The name of this attribute;
     */
    public const NAME = "for";

    /**
     * Process the directive and return the
     * output node.
     *
     * @return DOMNode|null
     */
    public function process(): ?\DOMNode
    {
        $directive = $this->getValue();

        if ($this->getElement()->hasAttributeNS(NamespacesRegistry::get("b:"), RepeatDirective::NAME)) {
            throw new TemplateException(
                "The b:repeat directive cannot be used with the b:for directive at the same time. " .
                    "Try to wrap your element with the <b:for> tag or the <b:foreach> tag instead."
            );
        }

        if (preg_match("/^(.+) in (.+)$/U", trim($directive), $m) != 0) {
            $key = null;
            $value = null;
            if (preg_match("/^\\(([a-zA-Z0-9_]+), ([a-zA-Z0-9_]+)\\)$/U", trim($m[1]), $kv) != 0) {
                $key = $kv[1];
                $value = $kv[2];
            } elseif (preg_match("/^([a-zA-Z0-9_]+)$/U", trim($m[1]), $kv) != 0) {
                $value = $kv[1];
            } else {
                throw new ParseErrorException("The b:for directive contains a bad command.");
            }

            if (preg_match(Template::DATA_MODEL_QUERY_REGEX, trim($m[2]), $it) == 0) {
                throw new ParseErrorException("The b:for directive contains a bad command.");
            }

            $iterator = $this->template->getResolver()->resolve($it[1]);

            $element = $this->document->createElement("b:fragment", "");

            /** @var \DOMElement */
            $toRepeat = $this->getElement()->cloneNode(true);
            $toRepeat->removeAttributeNode($toRepeat->getAttributeNodeNS(NamespacesRegistry::get("b:"), static::NAME));

            $innerHTML = $this->document->saveXML($toRepeat);

            $charsFilter = Template::DATA_MODEL_QUERY_CHARS_FILTER;

            foreach ($iterator as $k => $v) {
                $iterationHTML = preg_replace_callback(
                    Template::DATA_MODEL_QUERY_REGEX,
                    function (array $m) use ($value, $key, $it, $k, $v, $charsFilter) {
                        return preg_replace("#^\\\$\\{({$charsFilter}*){$value}\b#U", "\${\$1{$it[1]}[{$k}]", $m[0]);
                    },
                    $innerHTML
                );

                if ($key !== null) {
                    $iterationHTML = preg_replace("/\\\$\\{{$key}\\}/U", $k, $iterationHTML);
                }

                Utilities::appendHTML($element, $iterationHTML);
            }

            return $element;
        } elseif (preg_match("/^([a-zA-Z0-9_]+) from (.+) to (.+)$/U", trim($directive), $m) != 0) {
            $itemVar = $m[1];
            $itemStart = intval(Utilities::evaluate($m[2], $this->template->getResolver()));
            $itemEnd = intval(Utilities::evaluate($m[3], $this->template->getResolver()));

            /** @var \DOMElement */
            $toRepeat = $this->getElement()->cloneNode(true);
            $toRepeat->removeAttributeNode($toRepeat->getAttributeNodeNS(NamespacesRegistry::get("b:"), static::NAME));

            $innerHTML = $this->document->saveXML($toRepeat);

            $domElement = $this->document->createElement("b:fragment", "");

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
        } else {
            throw new ParseErrorException("The b:for directive contains a bad command.");
        }
    }
}
