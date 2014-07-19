<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 12.07.14
 * Time: 17:30
 */

namespace Actinarium\Finediff\Model;

/**
 * Immutable class that holds low index and high index
 *
 * @package Actinarium\Finediff\Util
 */
class Range
{
    /** @var int Sub-sequence starting index, <b>inclusive</b> */
    public $from;
    /** @var int Sub-sequence ending index, <b>exclusive</b> */
    public $to;

    /**
     * @param int $from Sub-sequence starting index, <b>inclusive</b>
     * @param int $to   Sub-sequence ending index, <b>exclusive</b>
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return int Range length
     */
    public function getLength()
    {
        return $this->to - $this->from;
    }
}
