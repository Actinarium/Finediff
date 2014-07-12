<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 12.07.14
 * Time: 17:11
 */

namespace Actinarium\Finediff\Impl;

use Actinarium\Finediff\Core\IndexedSequence;
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
     * @param IndexedSequence          $base     Base sequence, which the second sequence will be compared against. The
     *                                           base sequence is the one that gets indexed for faster search (given
     *                                           you use the default implementation of Sequence), so if you need to
     *                                           compare multiple sequences against one, use it as a base
     * @param Sequence                 $test     The sequence to compare against the base
     * @param StringComparisonStrategy $strategy The string comparison strategy used to verify whether strings are
     *                                           equal (can be one of bundled solutions or a custom one)
     * @param RangePair                $ranges   If set, look only within given range of items in given sequences (left
     *                                           range is for base, right range is for second sequence)
     *
     * @return RangePair|null Pair of ranges where match was found, or null in case of no match
     */
    public function find(IndexedSequence $base, Sequence $test, StringComparisonStrategy $strategy,
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

        // These are the variables we will store info about best match - starting index in A, B, and match size
        $bestTestLowIndex = 0;
        $bestBaseLowIndex = 0;
        $bestMatchLength = 0;

        // Now using the sliding pointer, for each string in tested sequence look up matching string indices in base
        // sequence. Then for each match find the length of the block. Current implementation assumes that all lines
        // in base are indexed, unlike difflib.py implementation where some lines are removed upon reaching index limit
        $pointer = $ranges->getRangeRight()->getIndexLow();
        $last = $ranges->getRangeRight()->getIndexHigh();
        while ($pointer <= $last) {
            $matchIndices = $base->findOccurrences($testArray[$pointer]);
            if ($matchIndices === null) {
                // No match found, increment the pointer and continue
                $pointer++;
                continue;
            }
            foreach ($matchIndices as $matchIndex) {
                // Ignore matches outside given index window
                if ($matchIndex < $ranges->getRangeLeft()->getIndexLow()) {
                    continue;
                } elseif ($matchIndex > $ranges->getRangeLeft()->getIndexHigh()) {
                    // Since indices are sorted, at this point we know we won't have any more matches in given window
                    break;
                }
                // Otherwise for current match look-ahead how many elements will contiguously match within window
                // from this starting point (offset is 1 because the element at current pointer is a match already)
                $offset = 1;
                while ($matchIndex + $offset <= $ranges->getRangeLeft()->getIndexHigh()
                    && $pointer + $offset <= $ranges->getRangeRight()->getIndexHigh()
                    && $strategy->areEqual($baseArray[$matchIndex + $offset], $testArray[$pointer + $offset])
                ) {
                    $offset++;
                }
                // At this point offset will hold the number of elements contiguously matched
                if ($offset > $bestMatchLength) {
                    $bestMatchLength = $offset;
                    $bestTestLowIndex = $pointer;
                    $bestBaseLowIndex = $matchIndex;
                }
                // Leap the pointer, because there's no reason to go over matched lines again
                $pointer += $offset;
            }
        }

        // If we were "optimizing" our index like difflib.py by removing popular dict items, we would add additional
        // expanding here (i.e. looking behind the bestLowIndex and expand the match). But we don't, so we're done

        if ($bestMatchLength === 0) {
            return null;
        } else {
            return new RangePair(
                new Range($bestBaseLowIndex, $bestBaseLowIndex + $bestMatchLength - 1),
                new Range($bestTestLowIndex, $bestTestLowIndex + $bestMatchLength - 1)
            );
        }
    }
} 
