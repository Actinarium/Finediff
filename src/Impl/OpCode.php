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
    private $operation;

    public function __construct(RangePair $pair = null)
    {
        if ($pair !== null) {
            $this->rangeLeft = $pair->getRangeLeft();
            $this->rangeRight = $pair->getRangeRight();
        }
    }

    /**
     * @param int $tag
     *
     * @return OpCode self-reference
     */
    public function setOperation($tag)
    {
        $this->operation = $tag;
        return $this;
    }

    /**
     * @return int
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Get a reverse operational code that defines a change from test sequence to base
     *
     * @return OpCode Operational code reverted to current
     */
    public function getReverse()
    {
        $opcode = new OpCode();
        $opcode->rangeLeft = $this->rangeRight;
        $opcode->rangeRight = $this->rangeLeft;
        if ($this->operation === self::INSERT || $this->operation === self::DELETE) {
            $opcode->operation = self::INSERT + self::DELETE - $this->operation;
        } else {
            $opcode->operation = $this->operation;
        }
        return $opcode;
    }
} 
