<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 12.07.14
 * Time: 18:38
 */
namespace Actinarium\Finediff\Core;

use Actinarium\Finediff\Impl\RangePair;


/**
 * An interface for longest common contiguous sub-sequence finder implementations
 *
 * @package Actinarium\Finediff\SequenceMatcher
 */
interface LCCSFinder
{
    /**
     * Compares two sequences to find the longest common contiguous sub-sequence. Supports setting the ranges of
     * sequences to search within so that we can pass the same sequence around with different ranges instead of
     * inefficiently copying arrays
     *
     * @param Sequence                 $base     Base sequence, which the second sequence will be compared against. The
     *                                           base sequence is the one that gets "hashed" for faster search (given
     *                                           you use the default
     *                                           implementation of Sequence), so if you need to compare multiple sequences against
     *                                           one, use it as a base
     * @param Sequence                 $test     The sequence to compare against the base
     * @param StringComparisonStrategy $strategy The string comparison strategy used to verify whether strings are
     *                                           equal (can be one of bundled solutions or a custom one)
     * @param RangePair                $ranges   If set, look only within given range of items in given sequences (left range is for
     *                                           base, right range is for second sequence)
     *
     * @return RangePair|null Pair of ranges where match was found, or null in case of no match
     */
    public function findLongestCommonSubSequence(Sequence $base, Sequence $test, StringComparisonStrategy $strategy,
                                                 RangePair $ranges = null);
}
