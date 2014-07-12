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
}
