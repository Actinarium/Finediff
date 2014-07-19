<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 15.07.14
 * Time: 1:41
 */

namespace Actinarium\Finediff\Util;


use Actinarium\Finediff\Model\OpCode;
use Actinarium\Finediff\Model\OpCodeGroup;
use Actinarium\Finediff\Model\Range;

final class OpCodeUtils
{

    private function __construct() { }

    /**
     * @param OpCode[] $opCodes
     *
     * @return OpCode[]
     */
    public static function revertOpCodes(array &$opCodes)
    {
        $out = array();
        foreach ($opCodes as &$opCode) {
            $out[] = $opCode->getReverse();
        }
        return $out;
    }

    /**
     * @param OpCode[] $opCodes
     * @param bool     $insertFirst If true, insert will come before delete, otherwise if false
     *
     * @return OpCode[]
     */
    public static function flattenReplaces(array &$opCodes, $insertFirst = false)
    {
        $out = array();
        foreach ($opCodes as &$opCode) {
            if ($opCode->getOperation() === OpCode::REPLACE) {
                if ($insertFirst) {
                    $from = $opCode->getRangeLeft()->getFrom();
                    $to = $opCode->getRangeRight()->getTo();
                    $insertOpCode = new OpCode(
                        new Range($from, $from),
                        $opCode->getRangeRight(),
                        OpCode::INSERT
                    );
                    $deleteOpCode = new OpCode(
                        $opCode->getRangeLeft(),
                        new Range($to, $to),
                        OpCode::DELETE
                    );
                    $out[] = $insertOpCode;
                    $out[] = $deleteOpCode;
                } else {
                    $from = $opCode->getRangeRight()->getFrom();
                    $to = $opCode->getRangeLeft()->getTo();
                    $deleteOpCode = new OpCode(
                        $opCode->getRangeLeft(),
                        new Range($from, $from),
                        OpCode::DELETE
                    );
                    $insertOpCode = new OpCode(
                        new Range($to, $to),
                        $opCode->getRangeRight(),
                        OpCode::INSERT
                    );
                    $out[] = $deleteOpCode;
                    $out[] = $insertOpCode;
                }
            } else {
                $out[] = $opCode;
            }
        }
        return $out;
    }

    /**
     * @param OpCode[] $opCodes       Opcodes to group
     * @param int      $context       Number of equal/ignored lines to include within non-equal opcode group
     * @param bool     $ignoreMatches Set true to return only groups for differing opcodes. If set to false and context
     *                                is more than 0, will shrink matching blocks by this many elements.
     *
     * @return OpCodeGroup[] Groups of opcode sequences
     */
    public static function groupOpCodes(array &$opCodes, $context = 0, $ignoreMatches = false)
    {
        /** @var OpCodeGroup[] $output */
        $output = array();

        $pointer = 0;
        $last = count($opCodes) - 1;
        while ($pointer < $last) {
            $group = new OpCodeGroup();

            $operation = $opCodes[$pointer]->getOperation();
            if ($operation === OpCode::EQUAL || $operation === OpCode::IGNORE) {
                // todo
            }
            // todo
        }
    }
}
