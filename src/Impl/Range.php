<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 12.07.14
 * Time: 17:30
 */

namespace Actinarium\Finediff\Impl;

/**
 * Immutable class that holds low index and high index
 *
 * @package Actinarium\Finediff\Util
 */
class Range
{
    /** @var  int */
    protected $indexLow;
    /** @var  int */
    protected $indexHigh;

    /**
     * @param int $indexLow  Sub-sequence starting index, inclusive
     * @param int $indexHigh Sub-sequence ending index, inclusive
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($indexLow, $indexHigh)
    {
        if (!is_int($indexLow) || !is_int($indexHigh)) {
            throw new \InvalidArgumentException("Both indices should be integers");
        }
        if ($indexLow > $indexHigh) {
            throw new \InvalidArgumentException("High index cannot be smaller than low index");
        }
        $this->indexLow = $indexLow;
        $this->indexHigh = $indexHigh;
    }

    /**
     * @return int
     */
    public function getIndexHigh()
    {
        return $this->indexHigh;
    }

    /**
     * @return int
     */
    public function getIndexLow()
    {
        return $this->indexLow;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->indexHigh - $this->indexLow + 1;
    }

    /**
     * @param int $indexHigh
     *
     * @return Range new object with given high index and new low index
     */
    public function setIndexHigh($indexHigh)
    {
        return new Range($this->indexLow, $indexHigh);
    }

    /**
     * @param int $indexLow
     *
     * @return Range new object with given low index and new high index
     */
    public function setIndexLow($indexLow)
    {
        return new Range($indexLow, $this->indexHigh);
    }

    /**
     * Increase low and decrease high pointers by offset
     *
     * @param int $offset
     *
     * @return Range|null Shrank range, or null if the range would cease as a result of shrink
     */
    public function shrink($offset)
    {
        $newLow = $this->indexLow + $offset;
        $newHigh = $this->indexHigh - $offset;
        if ($newLow <= $newHigh) {
            return new Range($newLow, $newHigh);
        } else {
            return null;
        }
    }

    /**
     * Decrease low and increase high pointers by offset within given bounds
     *
     * @param int        $offset
     * @param Range|null $bounds If given, will limit pointers to be within given range. If null, bottom will be limited
     *                           by 0, and top is not limited
     *
     * @return Range Expanded range
     */
    public function expand($offset, Range $bounds = null)
    {
        $newLow = $this->indexLow - $offset;
        $newHigh = $this->indexHigh + $offset;
        if ($bounds !== null) {
            if ($newLow < $bounds->getIndexLow()) {
                $newLow = $bounds->getIndexLow();
            }
            if ($newHigh > $bounds->getIndexHigh()) {
                $newHigh = $bounds->getIndexHigh();
            }
        } else {
            if ($newLow < 0) {
                $newLow = 0;
            }
        }
        return new Range($newLow, $newHigh);
    }
}
