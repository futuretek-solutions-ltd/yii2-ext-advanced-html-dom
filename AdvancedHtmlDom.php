<?php
namespace futuretek\shared\dom;

    /**
     * Website: http://sourceforge.net/projects/advancedhtmldom/
     * Description: A drop-in replacement for simple html dom
     *
     * Licensed under The MIT License
     * Redistributions of files must retain the above copyright notice.
     *
     * @author  P Guardiario <pguardiario@gmail.com>
     * @version 0.0.11
     */
/**
 * Full code revision
 *
 * @author  Petr Leo Compel <petr.compel@futuretek.cz>
 * @version 0.9.5
 */

use DOMDocument;
use DOMXPath;

$attributes = array('href', 'src', 'id', 'class', 'name', 'text', 'height', 'width', 'content', 'value', 'title', 'alt');

$tags = array(
    'a',
    'abbr',
    'address',
    'area',
    'article',
    'aside',
    'audio',
    'b',
    'base',
    'blockquote',
    'body',
    'br',
    'button',
    'canvas',
    'caption',
    'cite',
    'code',
    'col',
    'colgroup',
    'data',
    'datalist',
    'dd',
    'detail',
    'dialog',
    'div',
    'dl',
    'dt',
    'em',
    'embed',
    'fieldset',
    'figcaption',
    'figure',
    'footer',
    'form',
    'font',
    'frame',
    'frameset',
    'h1',
    'h2',
    'h3',
    'h4',
    'h5',
    'h6',
    'head',
    'header',
    'hgroup',
    'hr',
    'html',
    'i',
    'iframe',
    'img',
    'image',
    'input',
    'label',
    'legend',
    'li',
    'map',
    'mark',
    'menu',
    'meta',
    'nav',
    'noscript',
    'object',
    'ol',
    'optgroup',
    'option',
    'p',
    'param',
    'pre',
    'script',
    'section',
    'select',
    'small',
    'source',
    'span',
    'strong',
    'style',
    'sub',
    'sup',
    'table',
    'tbody',
    'td',
    'textarea',
    'tfoot',
    'th',
    'thead',
    'title',
    'tr',
    'track',
    'u',
    'ul',
    'var',
    'video'
);

/**
 *
 */
define('TAG_REGEX', '/^(' . implode('|', $tags) . ')$/');
/**
 *
 */
define('TAGS_REGEX', '/^(' . implode('|', $tags) . ')e?s$/');

/**
 *
 */
define('ATTRIBUTE_REGEX', '/^(' . implode('|', $attributes) . '|data-\w+)$/');
/**
 *
 */
define('ATTRIBUTES_REGEX', '/^(' . implode('|', $attributes) . '|data-\w+)e?s$/');

/**
 * Class AdvancedHtmlDom
 */
class AdvancedHtmlDom extends AdvancedHtmlBase
{
    /**
     * @var DOMXPath
     */
    public $xpath;
    /**
     * @var
     */
    public $root;

    /**
     * AdvancedHtmlDom constructor.
     *
     * @param null       $html
     * @param bool|false $is_xml
     */
    public function __construct($html = null, $is_xml = false)
    {
        $this->doc = $this;
        if ($html) {
            $this->load($html, $is_xml);
        }
    }

    /**
     * @param            $html
     * @param bool|false $is_xml
     */
    public function load($html, $is_xml = false)
    {
        $this->dom = new DOMDocument();
        if ($is_xml) {
            @$this->dom->loadXML(preg_replace('/xmlns=".*?"/ ', '', $html));
        } else {
            @$this->dom->loadHTML($html);
        }
        $this->xpath = new DOMXPath($this->dom);
        //$this->root = new AHTMLNode($this->dom->documentElement, $this->doc);
        $this->root = $this->at('body');
    }

    /**
     * @param            $file
     * @param bool|false $is_xml
     */
    public function load_file($file, $is_xml = false)
    {
        $this->load(file_get_contents($file), $is_xml);
    }

    // special cases
    /**
     * @return mixed
     */
    public function text()
    {
        return $this->root->text;
    }

    /**
     * @return mixed
     */
    public function title()
    {
        return $this->at('title')->text();
    }

    /**
     * @param $html
     *
     * @return AdvancedHtmlDom
     */
    public static function str_get_html($html)
    {
        return new AdvancedHtmlDom($html);
    }

    /**
     * @param $url
     *
     * @return AdvancedHtmlDom
     */
    public static function file_get_html($url)
    {
        return self::str_get_html(file_get_contents($url));
    }

    /**
     * @param $html
     *
     * @return AdvancedHtmlDom
     */
    public static function str_get_xml($html)
    {
        return new AdvancedHtmlDom($html, true);
    }

    /**
     * @param $url
     *
     * @return AdvancedHtmlDom
     */
    public static function file_get_xml($url)
    {
        return self::str_get_xml(file_get_contents($url));
    }

}