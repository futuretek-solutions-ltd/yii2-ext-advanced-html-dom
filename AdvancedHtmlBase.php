<?php
namespace futuretek\shared\dom;

use DOMDocument;
use DOMElement;

/**
 * Class AdvancedHtmlBase
 *
 * magic functions
 *
 * @method mixed at($argument1) {
 *     TBD
 *
 *     @param string $argument1 TBD
 *
 *     @return mixed
 * }
 *
 * @method mixed search($argument1) {
 *     TBD
 *
 *     @param string $argument1 TBD
 *
 *     @return mixed
 * }
 * @method mixed getAttribute($argument1) {
 *     TBD
 *
 *     @param string $argument1 TBD
 *
 *     @return mixed
 * }
 * @method mixed setAttribute($argument1) {
 *     TBD
 *
 *     @param string $argument1 TBD
 *
 *     @return mixed
 * }
 * @method mixed removeAttribute($argument1) {
 *     TBD
 *
 *     @param string $argument1 TBD
 *
 *     @return mixed
 * }
 *
 * ---
 *
 * Virtual attrs
 * @property string $innertext
 * @property string $innerhtml
 * @property string $outertext
 * @property string $plaintext
 * @property AHTMLNode $parent
 * @property AHTMLNode $parentNode
 * @property AHTMLNode $children
 * @property AHTMLNode[] $childnodes
 *
 */
class AdvancedHtmlBase
{
    /**
     * @var AdvancedHtmlDom
     */
    public $doc;
    /**
     * @var DOMDocument
     */
    public $dom;
    /**
     * @var DOMElement
     */
    public $node;
    /**
     * @var bool
     */
    public $is_text = false;

    /**
     * @return mixed
     */
    public function text()
    {
        return $this->node->nodeValue;
    }

    /**
     * @return string
     */
    public function html()
    {
        return $this->doc->dom->saveHTML($this->node);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->html();
    }

    /**
     * @return $this
     */
    public function remove()
    {
        $this->node->parentNode->removeChild($this->node);

        return $this;
    }

    /**
     * @param $str
     *
     * @return Str
     */
    public function str($str)
    {
        return new Str($str);
    }

    /**
     * @param $str
     *
     * @return string
     */
    public function clean($str)
    {
        return trim(preg_replace('/\s+/', ' ', $str));
    }

    /**
     * @param $str
     *
     * @return string
     */
    public function trim($str)
    {
        return trim($str);
    }

    /**
     * @param      $css
     * @param null|int $index
     *
     * @return AHTMLNode|AHTMLNodeList|null
     */
    public function find($css, $index = null)
    {
        $xpath = CSS::xpath_for($css);
        if ($this->doc === null || $this->doc->xpath === null) {
            return null;
        }
        if (null === $index) {
            return new AHTMLNodeList($this->doc->xpath->query($xpath, $this->node), $this->doc);
        } else {
            $nl = $this->doc->xpath->query($xpath, $this->node);
            if ($index < 0) {
                $index = $nl->length + $index;
            }
            $node = $nl->item($index);

            return $node ? new AHTMLNode($node, $this->doc) : null;
        }
    }

    // magic methods
    /**
     * @param $key
     * @param $args
     *
     * @return mixed|void
     */
    public function __call($key, $args)
    {
        $key = strtolower(str_replace('_', '', $key));
        switch ($key) {
        case 'innertext':
            return ($this->is_text || !$this->children->length) ? $this->text() : $this->find('./text()|./*')->outertext;
        case 'plaintext':
            return $this->text();
        case 'outertext':
        case 'html':
        case 'save':
            return $this->html();
        case 'innerhtml':
            $ret = '';
            foreach ($this->node->childNodes as $child) {
                $ret .= $this->doc->dom->saveHTML($child);
            }

            return $ret;

        case 'tag':
            return $this->node->nodeName;
        case 'next':
            return $this->at('./following-sibling::*[1]|./following-sibling::text()[1]|./following-sibling::comment()[1]');

        case 'index':
            return $this->search('./preceding-sibling::*')->length + 1;

            /*
            DOMNode::insertBefore � Adds a new child
            */

            // simple-html-dom junk methods
        case 'clear':
            return null;

            // search functions
        case 'at':
        case 'getelementbytagname':
            return $this->find($args[0], 0);

        case 'search':
        case 'getelementsbytagname':
            return array_key_exists(1, $args) ? $this->find($args[0], $args[1]) : $this->find($args[0]);

        case 'getelementbyid':
            return $this->find('#' . $args[0], 0);
        case 'getelementsbyid':
            return array_key_exists(1, $args) ? $this->find('#' . $args[0], $args[1]) : $this->find('#' . $args[0]);

            // attributes
        case 'hasattribute':
            return !$this->is_text && $this->node->getAttribute($args[0]);
        case 'getattribute':
            return $this->$args[0];
        case 'setattribute':
            return $this->$args[0] = $args[1];
        case 'removeattribute':
            return $this->$args[0] = null;

            // wrap
        case 'wrap':
            return $this->replace('<' . $args[0] . '>' . $this . '</' . $args[0] . '>');
        case 'unwrap':
            return $this->parent->replace($this);

        case 'str':
            return new Str($this->text);

            // heirarchy
        case 'firstchild':
            return $this->at('> *');
        case 'lastchild':
            return $this->at('> *:last');
        case 'nextsibling':
            return $this->at('+ *');
        case 'prevsibling':
            return $this->at('./preceding-sibling::*[1]');
        case 'parent':
            return $this->at('./..');
        case 'children':
        case 'childnodes':
            $nl = $this->search('./*');

            return array_key_exists(0, $args) ? $nl[$args[0]] : $nl;
        case 'child': // including text/comment nodes
            $nl = $this->search('./*|./text()|./comment()');

            return array_key_exists(0, $args) ? $nl[$args[0]] : $nl;

        }

        // $doc->spans[x]
        if (preg_match(TAGS_REGEX, $key, $m)) {
            return $this->find($m[1]);
        }
        if (preg_match(TAG_REGEX, $key, $m)) {
            return $this->find($m[1], 0);
        }

        if (preg_match('/(clean|trim|str)(.*)/', $key, $m)) {
            return $this->$m[1]($this->$m[2]);
        }

        if (!preg_match(ATTRIBUTE_REGEX, $key, $m)) {
            trigger_error('Unknown method or property: ' . $key, E_USER_WARNING);
        }
        if (!$this->node || $this->is_text) {
            return null;
        }

        return $this->node->getAttribute($key);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->$key();
    }
}