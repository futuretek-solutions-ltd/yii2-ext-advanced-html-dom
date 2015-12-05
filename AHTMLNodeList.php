<?php
namespace futuretek\shared\dom;
/**
 * Created by PhpStorm.
 * User: petrleocompel
 * Date: 05.12.15
 * Time: 14:19
 */
use Iterator;
use Countable;
use ArrayAccess;
use DOMNodeList;

/**
 * Class AHTMLNodeList
 */
class AHTMLNodeList implements Iterator, Countable, ArrayAccess
{
    /**
     * @var DOMNodeList
     */
    private $nodeList;
    /**
     * @var
     */
    private $doc;
    /**
     * @var int
     */
    private $counter = 0;

    /**
     * AHTMLNodeList constructor.
     *
     * @param $nodeList
     * @param $doc
     */
    public function __construct($nodeList, $doc)
    {
        $this->nodeList = $nodeList;
        $this->doc = $doc;
    }

    /*
    abstract public boolean offsetExists ( mixed $offset )
    abstract public mixed offsetGet ( mixed $offset )
    abstract public void offsetSet ( mixed $offset , mixed $value )
    abstract public void offsetUnset ( mixed $offset )
    */

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return 0 <= $offset && $offset < $this->nodeList->length();
    }

    /**
     * @param mixed $offset
     *
     * @return AHTMLNode
     */
    public function offsetGet($offset)
    {
        return new AHTMLNode($this->nodeList->item($offset), $this->doc);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        trigger_error('offsetSet not implemented', E_USER_WARNING);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        trigger_error('offsetUnset not implemented', E_USER_WARNING);
    }

    /**
     * @return mixed
     */
    public function count()
    {
        return $this->nodeList->length;
    }

    /**
     *
     */
    public function rewind()
    {
        $this->counter = 0;
    }

    /**
     * @return AHTMLNode
     */
    public function current()
    {
        return new AHTMLNode($this->nodeList->item($this->counter), $this->doc);
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->counter;
    }

    /**
     *
     */
    public function next()
    {
        $this->counter++;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->counter < $this->nodeList->length;
    }

    /**
     * @return AHTMLNode|null
     */
    public function last()
    {
        return ($this->nodeList->length > 0) ? new AHTMLNode($this->nodeList->item($this->nodeList->length - 1), $this->doc) : null;
    }

    /**
     * @return $this
     */
    public function remove()
    {
        foreach ($this as $node) {
            $node->remove();
        }

        return $this;
    }

    /**
     * @param $c
     *
     * @return array
     */
    public function map($c)
    {
        $ret = array();
        foreach ($this as $node) {
            $ret[] = $c($node);
        }

        return $ret;
    }


    //math methods
    /**
     * @param        $nl
     * @param string $op
     *
     * @return AHTMLNodeList
     */
    public function doMath($nl, $op = 'plus')
    {
        $paths = array();
        $other_paths = array();

        foreach ($this as $node) {
            $paths[] = $node->node->getNodePath();
        }
        foreach ($nl as $node) {
            $other_paths[] = $node->node->getNodePath();
        }
        switch ($op) {
        case 'plus':
            $new_paths = array_unique(array_merge($paths, $other_paths));
            break;
        case 'minus':
            $new_paths = array_diff($paths, $other_paths);
            break;
        case 'intersect':
            $new_paths = array_intersect($paths, $other_paths);
            break;
        }

        return new AHTMLNodeList($this->doc->xpath->query(implode('|', $new_paths)), $this->doc);
    }

    /**
     * @param $nl
     *
     * @return AHTMLNodeList
     */
    public function minus($nl)
    {
        return $this->doMath($nl, 'minus');
    }

    /**
     * @param $nl
     *
     * @return AHTMLNodeList
     */
    public function plus($nl)
    {
        return $this->doMath($nl, 'plus');
    }

    /**
     * @param $nl
     *
     * @return AHTMLNodeList
     */
    public function intersect($nl)
    {
        return $this->doMath($nl, 'intersect');
    }


    // magic methods
    /**
     * @param $key
     * @param $values
     *
     * @return array|string
     */
    public function __call($key, $values)
    {
        $key = strtolower(str_replace('_', '', $key));
        switch ($key) {
        case 'to_a':
            $returnValue = array();
            foreach ($this as $node) {
                $returnValue[] = new AHTMLNode($this->nodeList->item($this->counter), $this->doc);
            }

            return $returnValue;
        }
        // otherwise

        $returnValue = array();

        /*
            if(preg_match(TAGS_REGEX, $key, $m)) return $this->find($m[1]);
            if(preg_match(TAG_REGEX, $key, $m)) return $this->find($m[1], 0);
        */

        if (preg_match(ATTRIBUTES_REGEX, $key, $m) || preg_match('/^((clean|trim|str).*)s$/', $key, $m)) {
            foreach ($this as $node) {
                $returnValue[] = $node->$m[1];
            }

            return $returnValue;
        }

        if (preg_match(ATTRIBUTE_REGEX, $key, $m)) {
            foreach ($this as $node) {
                $returnValue[] = $node->$m[1];
            }

            return implode('', $returnValue);
        }

        // what now?
        foreach ($this as $node) {
            $returnValue[] = isset($values[0]) ? $node->$key($values[0]) : $node->$key();
        }

        return implode('', $returnValue);
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

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->html();
    }

    /**
     * @return mixed
     */
    public function length()
    {
        return $this->nodeList->length;
    }
}
