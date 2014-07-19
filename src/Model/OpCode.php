<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 14.07.14
 * Time: 2:29
 */

namespace Actinarium\Finediff\Model;


class OpCode extends RangePair
{
    const IGNORE = 'x';
    const EQUAL = 'e';
    const INSERT = 'i';
    const DELETE = 'd';
    const REPLACE = 'r';

    /** @var string */
    private $operation;

    /**
     * @param Range $rangeLeft
     * @param Range $rangeRight
     * @param string $operation
     */
    function __construct(Range $rangeLeft, Range $rangeRight, $operation)
    {
        $this->rangeLeft = $rangeLeft;
        $this->rangeRight = $rangeRight;
        $this->operation = $operation;
    }

    /**
     * @return string
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
        if ($this->operation === self::INSERT) {
            $operation = self::DELETE;
        } elseif ($this->operation === self::DELETE) {
            $operation = self::INSERT;
        } else {
            $operation = $this->operation;
        }
        return new OpCode($this->rangeRight, $this->rangeLeft, $operation);
    }
} 
