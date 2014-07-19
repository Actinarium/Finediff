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
    /** @var  int */
    protected $from;
    /** @var  int */
    protected $to;

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
     * @return int
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return int
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->to - $this->from;
    }

    /**
     * @param int $indexHigh
     *
     * @return Range new object
     */
    public function setTo($indexHigh)
    {
        return new Range($this->from, $indexHigh);
    }

    /**
     * @param int $indexLow
     *
     * @return Range new object
     */
    public function setFrom($indexLow)
    {
        return new Range($indexLow, $this->to);
    }
}
