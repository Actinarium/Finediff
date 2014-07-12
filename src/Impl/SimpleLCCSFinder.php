<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 12.07.14
 * Time: 17:11
 */

namespace Actinarium\Finediff\Impl;

use Actinarium\Finediff\Core\LCCSFinder;
use Actinarium\Finediff\Core\Sequence;
use Actinarium\Finediff\Core\StringComparisonStrategy;

/**
 * A simple implementation of longest common contiguous sub-sequence finder based on <a href=
 * "http://collaboration.cmc.ec.gc.ca/science/rpn/biblio/ddj/Website/articles/DDJ/1988/8807/8807c/8807c.htm">Gestalt
 * pattern matching approach</a> by John W. Ratcliff and John A. Obershelp
 *
 * @package Actinarium\Finediff\SequenceMatcher
 */
class SimpleLCCSFinder implements LCCSFinder
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
                                                 RangePair $ranges = null)
    {
        // If ranges are not set, initialize them with zero to last indices of given sequences
        if ($ranges == null) {
            $ranges = new RangePair(
                new Range(0, $base->getLength() - 1),
                new Range(0, $test->getLength() - 1)
            );
        }

        // Pull out arrays from sequences, for faster access
        $baseArray = & $base->getSequence();
        $testArray = & $test->getSequence();

        // Initialize "best matches" with low indices, and "best match length" with 0
        $bestLowIndexBase = $ranges->getRangeLeft()->getIndexLow();
        $bestLowIndexSecond = $ranges->getRangeRight()->getIndexLow();
        $bestMatchLength = 0;

        // Now using the sliding pointer, for each string in second sequence look up the matching string in base
        // sequence and expand it while the blocks match
        $testPointer = $ranges->getRangeRight()->getIndexLow();
        $testUntil = $ranges->getRangeRight()->getIndexHigh();
        while ($testPointer <= $testUntil) {
            // todo
            $testPointer++;
        }
    }
} 
