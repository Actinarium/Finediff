<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 14.07.14
 * Time: 2:29
 */

namespace Actinarium\Finediff\Model;


class OpCode
{
    const IGNORE = 'x';
    const EQUAL = 'e';
    const INSERT = 'i';
    const DELETE = 'd';
    const REPLACE = 'r';

    /** @var int */
    private $operation;
    /** @var int */
    private $leftLength;
    /** @var int */
    private $rightLength;

    function __construct($operation, $leftLength, $rightLength)
    {
        $this->operation = $operation;
        $this->leftLength = $leftLength;
        $this->rightLength = $rightLength;
    }

    /**
     * @return int
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @return int
     */
    public function getLeftLength()
    {
        return $this->leftLength;
    }

    /**
     * @return int
     */
    public function getRightLength()
    {
        return $this->rightLength;
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
        return new OpCode($this->rightLength, $this->leftLength, $operation);
    }
} 
