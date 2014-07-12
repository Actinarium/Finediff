<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 12.07.14
 * Time: 17:35
 */

namespace Actinarium\Finediff\Impl;

/**
 * Holds ranges for two sequences
 *
 * @package Actinarium\Finediff\Util
 */
class RangePair
{
    /** @var  Range */
    protected $rangeLeft;
    /** @var  Range */
    protected $rangeRight;

    function __construct(Range $rangeLeft, Range $rangeRight)
    {
        $this->rangeLeft = $rangeLeft;
        $this->rangeRight = $rangeRight;
    }

    /**
     * @param Range $rangeLeft
     *
     * @return RangePair self-reference
     */
    public function setRangeLeft(Range $rangeLeft)
    {
        $this->rangeLeft = $rangeLeft;
        return $this;
    }

    /**
     * @return Range
     */
    public function getRangeLeft()
    {
        return $this->rangeLeft;
    }

    /**
     * @param Range $rangeRight
     *
     * @return RangePair self-reference
     */
    public function setRangeRight(Range $rangeRight)
    {
        $this->rangeRight = $rangeRight;
        return $this;
    }

    /**
     * @return Range
     */
    public function getRangeRight()
    {
        return $this->rangeRight;
    }
}
