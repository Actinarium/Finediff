<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 14.07.14
 * Time: 4:08
 */

namespace Actinarium\Finediff\Impl;


class BlocksMetadata
{

    /** @var  RangePair[] */
    private $matchingBlocks;
    /** @var  int */
    private $lengthLeft;
    /** @var  int */
    private $lengthRight;

    /**
     * @param RangePair[] $matchingBlocks Matching blocks
     * @param int         $lengthLeft     Length of left sequence
     * @param int         $lengthRight    Length of right sequence
     */
    function __construct(array &$matchingBlocks, $lengthLeft, $lengthRight)
    {
        $this->matchingBlocks = & $matchingBlocks;
        $this->lengthLeft = $lengthLeft;
        $this->lengthRight = $lengthRight;
    }

    /**
     * @return int
     */
    public function getLengthLeft()
    {
        return $this->lengthLeft;
    }

    /**
     * @return int
     */
    public function getLengthRight()
    {
        return $this->lengthRight;
    }

    /**
     * @return RangePair[]
     */
    public function &getMatchingBlocks()
    {
        return $this->matchingBlocks;
    }
}
