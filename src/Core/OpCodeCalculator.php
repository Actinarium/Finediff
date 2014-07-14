<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 15.07.14
 * Time: 1:06
 */
namespace Actinarium\Finediff\Core;

use Actinarium\Finediff\Impl\OpCode;

interface OpCodeCalculator
{
    /**
     * Calculate sequence of opcodes for given matching blocks
     *
     * @param IndexedSequence $base     Base sequence, which the second sequence will be compared against. The
     *                                  base sequence is the one that gets indexed for faster search (given
     *                                  you use the default implementation of Sequence), so if you need to
     *                                  compare multiple sequences against one, use it as a base
     * @param Sequence        $test     The sequence to compare against the base
     *
     * @return OpCode[] Sequence of opcodes
     */
    public function getOpCodes(IndexedSequence $base, Sequence $test);
}
