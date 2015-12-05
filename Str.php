<?php
namespace futuretek\shared\dom;
/**
 * Created by PhpStorm.
 * User: petrleocompel
 * Date: 05.12.15
 * Time: 14:21
 */
/**
 * PHPStr - Add regex functionality to PHP strings
 * Website: https://github.com/monkeysuffrage/phpstr
 *
 * @author  P Guardiario <pguardiario@gmail.com>
 * @version 0.2
 */

/**
 * Str
 */
class Str
{
    /**
     * @var string
     */
    public $text;

    /**
     * Str constructor.
     *
     * @param $str
     */
    public function __construct($str)
    {
        $this->text = $str;
    }

    /**
     * @param     $regex
     * @param int $group_number
     *
     * @return bool|Str
     */
    public function match($regex, $group_number = 0)
    {
        if (!preg_match($regex, $this->text, $m)) {
            return false;
        }
        $val = $m[$group_number];

        return new Str($val);
    }

    /**
     * @param     $regex
     * @param int $group_number
     *
     * @return mixed
     */
    public function scan($regex, $group_number = 0)
    {
        preg_match_all($regex, $this->text, $m);

        return $m[$group_number];
    }

    /**
     * @param     $regex
     * @param     $replacement
     * @param int $limit
     *
     * @return Str
     */
    public function gsub($regex, $replacement, $limit = -1)
    {
        if ('Closure' === @get_class($replacement)) {
            $val = preg_replace_callback($regex, $replacement, $this->text, $limit);
        } else {
            $val = preg_replace($regex, $replacement, $this->text, $limit);
        }

        return new Str($val);
    }

    /**
     * @param $regex
     * @param $replacement
     *
     * @return Str
     */
    public function sub($regex, $replacement)
    {
        $val = $this->gsub($regex, $replacement, 1);

        return new Str($val);
    }

    /**
     * @param     $regex
     * @param int $limit
     *
     * @return array
     */
    public function split($regex, $limit = -1)
    {
        return preg_split($regex, $this->text, $limit);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->text;
    }

    /**
     * @return mixed
     */
    public function to_s()
    {
        return $this->text;
    }
}