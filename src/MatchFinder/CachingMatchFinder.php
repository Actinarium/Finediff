<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 12.07.14
 * Time: 17:11
 */

namespace Actinarium\Finediff\MatchFinder;

use Actinarium\Finediff\Comparison\StringComparisonStrategy;
use Actinarium\Finediff\Model\Range;
use Actinarium\Finediff\Model\RangePair;
use Actinarium\Finediff\Sequence\IndexedSequence;
use Actinarium\Finediff\Sequence\Sequence;

/**
 * A modification of {@link DefaultMatchFinder} that uses cache to speed up execution at memory usage cost. Unlike the
 * named one, this matcher should be used as an instance for each calculation, because the cache is stored in
 * the instance. Also it's assumed that the first {@link #find()} call is about the whole range (this is when the cache
 * is created), and then matches are removed from the cache when finds are performed in a recursive manner.
 *
 * @package Actinarium\Finediff\SequenceMatcher
 */
class CachingMatchFinder implements MatchFinder
{
    /** @var  StringComparisonStrategy */
    private $strategy;
    /** @var array|null */
    private $cache;

    /**
     * @param StringComparisonStrategy $strategy The string comparison strategy used to verify whether strings are
     *                                           equal (can be one of bundled solutions or a custom one)
     */
    public function __construct(StringComparisonStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * Compares two sequences to find the longest common contiguous sub-sequence. Supports setting the ranges of
     * sequences to search within so that we can pass the same sequence around with different ranges instead of
     * inefficiently copying arrays
     *
     * @param IndexedSequence $base   Base sequence, which the second sequence will be compared against. The
     *                                base sequence is the one that gets indexed for faster search (given
     *                                you use the default implementation of Sequence), so if you need to
     *                                compare multiple sequences against one, use it as a base
     * @param Sequence        $test   The sequence to compare against the base
     * @param RangePair       $ranges If set, look only within given range of items in given sequences (left
     *                                range is for base, right range is for second sequence)
     *
     * @return RangePair|null Pair of ranges where match was found, or null in case of no match
     */
    public function find(IndexedSequence $base, Sequence $test, RangePair $ranges = null)
    {
        // If ranges are not set, initialize them with zero to last indices of given sequences.
        if ($ranges == null) {
            $ranges = new RangePair(
                new Range(0, $base->getLength()),
                new Range(0, $test->getLength())
            );
        }

        // Initialize cache if not initialized yet
        if ($this->cache === null) {
            $this->collectCache($base, $test);
        }

        // These are the variables we will store info about best match - starting index in A, B, and match size
        $bestTestLowIndex = 0;
        $bestMatchLength = 0;
        $bestBaseLowIndex = 0;

        // Find best match in the cache within given window
        foreach ($this->cache as $testIndex => $matchesInBase) {
            if ($testIndex < $ranges->getRangeRight()->getFrom()) {
                continue;
            } elseif ($testIndex >= $ranges->getRangeRight()->getTo()) {
                break;
            }
            foreach ($matchesInBase as $baseIndex => $length) {
                if ($baseIndex < $ranges->getRangeLeft()->getFrom()) {
                    continue;
                } elseif ($baseIndex >= $ranges->getRangeLeft()->getTo()) {
                    break;
                }
                if ($length > $bestMatchLength) {
                    $bestMatchLength = $length;
                    $bestTestLowIndex = $testIndex;
                    $bestBaseLowIndex = $baseIndex;
                }
            }
        }

        if ($bestMatchLength === 0) {
            return null;
        } else {
            // Remove best match from cache to speed up subsequent lookup
            unset($this->cache[$bestTestLowIndex]);
            return new RangePair(
                new Range($bestBaseLowIndex, $bestBaseLowIndex + $bestMatchLength),
                new Range($bestTestLowIndex, $bestTestLowIndex + $bestMatchLength)
            );
        }
    }

    private function collectCache(IndexedSequence $base, Sequence $test)
    {
        // Initialize the range to be all lines for both sequences
        $ranges = new RangePair(
            new Range(0, $base->getLength()),
            new Range(0, $test->getLength())
        );

        // Initialize the cache
        $this->cache = array();

        // Pull out arrays from sequences, for faster access
        $baseArray = & $base->getSequence();
        $testArray = & $test->getSequence();

        // Using the sliding pointer, for each string in tested sequence look up matching string indices in base
        // sequence. Put every found sequence in the cache
        $pointer = $ranges->getRangeRight()->getFrom();
        $last = $ranges->getRangeRight()->getTo();
        while ($pointer < $last) {
            $matchIndices = $base->findOccurrences($testArray[$pointer]);
            if ($matchIndices === null) {
                // No match found, move on to the next line
                $pointer++;
                continue;
            }

            // Otherwise for current match look-ahead how many elements will contiguously match from this starting
            // point (offset is 1 because the element at current pointer is a match already)
            foreach ($matchIndices as &$matchIndex) {
                $offset = 1;
                while ($matchIndex + $offset < $ranges->getRangeLeft()->getTo()
                    && $pointer + $offset < $ranges->getRangeRight()->getTo()
                    && $this->strategy->areEqual(
                                      $baseArray[$matchIndex + $offset],
                                      $testArray[$pointer + $offset]
                    )
                ) {
                    $offset++;
                }

                // If we are "optimizing" our index like difflib.py by removing frequent dict items, we should also do
                // look-behind here to find skipped but real (non-indexed) matches before the block
                $negativeOffset = 0;
                if (!$base->isIndexComplete()) {
                    $negativeOffset = 1;
                    while ($matchIndex - $negativeOffset >= $ranges->getRangeLeft()->getFrom()
                        && $pointer - $negativeOffset >= $ranges->getRangeRight()->getFrom()
                        && $this->strategy->areEqual(
                                          $baseArray[$matchIndex - $negativeOffset],
                                          $testArray[$pointer - $negativeOffset]
                        )
                    ) {
                        $negativeOffset++;
                    }
                }

                // At this point offset will hold the number of elements contiguously matched, so add it to the cache
                $testIndex = $pointer - $negativeOffset;
                $baseIndex = $matchIndex - $negativeOffset;
                $length = $offset + $negativeOffset;
                if (!isset($this->cache[$testIndex])) {
                    $this->cache[$testIndex] = array();
                }
                $this->cache[$testIndex][$baseIndex] = $length;

                // Leap the pointer forward, because there's no reason to go over matched lines again
                $pointer += $offset;
            }
        }
    }
} 
