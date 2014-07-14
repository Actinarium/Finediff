<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 14.07.14
 * Time: 2:29
 */

namespace Actinarium\Finediff\Impl;


class OpCode extends RangePair
{
    const IGNORE = 0;
    const EQUAL = 1;
    const INSERT = 2;
    const DELETE = 3;
    const REPLACE = 4;

    /** @var  int */
    private $tag;

    public function __construct(RangePair $pair)
    {
        $this->rangeLeft = $pair->getRangeLeft();
        $this->rangeRight = $pair->getRangeRight();
    }

    /**
     * @param int $tag
     *
     * @return OpCode self-reference
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @return int
     */
    public function getTag()
    {
        return $this->tag;
    }
} 
