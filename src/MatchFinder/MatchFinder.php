<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 12.07.14
 * Time: 18:38
 */
namespace Actinarium\Finediff\MatchFinder;

use Actinarium\Finediff\Model\RangePair;
use Actinarium\Finediff\Sequence\IndexedSequence;
use Actinarium\Finediff\Sequence\Sequence;


/**
 * An interface for longest common contiguous sub-sequence finder implementations
 *
 * @package Actinarium\Finediff\SequenceMatcher
 */
interface MatchFinder
{
    /**
     * Compares two sequences to find the longest common contiguous sub-sequence. Supports setting the ranges of
     * sequences to search within so that we can pass the same sequence around with different ranges instead of
     * inefficiently copying arrays






*
*@param IndexedSequence $base     Base sequence, which the second sequence will be compared against. The
     *                                  base sequence is the one that gets indexed for faster search (given
     *                                  you use the default implementation of Sequence), so if you need to
     *                                  compare multiple sequences against one, use it as a base
     * @param \Actinarium\Finediff\Sequence\Sequence        $test     The sequence to compare against the base
     * @param \Actinarium\Finediff\Model\RangePair       $ranges   If set, look only within given range of items in given sequences (left
     *                                  range is for base, right range is for second sequence)






*
* @return \Actinarium\Finediff\Model\RangePair|null Pair of ranges where match was found, or null in case of no match
     */
    public function find(IndexedSequence $base, Sequence $test, RangePair $ranges = null);
}
