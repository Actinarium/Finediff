<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 13.07.14
 * Time: 15:53
 */

namespace Actinarium\Finediff\Impl;


use Actinarium\Finediff\Core\IndexedSequence;
use Actinarium\Finediff\Core\LCCSFinder;
use Actinarium\Finediff\Core\Sequence;

class DefaultOpCodeCalculator
{
    /** @var  LCCSFinder */
    private $matchFinder;

    public function __construct(LCCSFinder $matchFinder)
    {
        $this->matchFinder = $matchFinder;
    }

    /**
     * Find matching blocks
     *
     * @param IndexedSequence $base     Base sequence, which the second sequence will be compared against. The
     *                                  base sequence is the one that gets indexed for faster search (given
     *                                  you use the default implementation of Sequence), so if you need to
     *                                  compare multiple sequences against one, use it as a base
     * @param Sequence        $test     The sequence to compare against the base
     *
     * @return RangePair[] Range pairs for blocks that matched in both sequences, sorted by their appearance order.
     *                     Left range always corresponds to base, right range corresponds to test
     */
    public function getMatchingBlocks(IndexedSequence $base, Sequence $test)
    {
        $fullRanges = new RangePair(
            new Range(0, $base->getLength() - 1),
            new Range(0, $test->getLength() - 1)
        );

        // Initialize empty array for matching blocks
        /** @var RangePair[] $matchingBlocks */
        $matchingBlocks = array();

        // Instead of finding matches recursively, use the stack to store blocks between matches.
        // todo: find out whether stack or queue would be more efficient for further sorting of matched blocks
        $stack = array($fullRanges);
        while (!empty($stack)) {
            /** @var RangePair $currentWindow */
            $currentWindow = array_pop($stack);
            $match = $this->matchFinder->find($base, $test, $currentWindow);
            if ($match !== null) {
                $matchingBlocks[] = $match;

                // If both sequences have elements to the left of the matched block, push it to the stack
                if ($match->getRangeLeft()->getIndexLow() > $currentWindow->getRangeLeft()->getIndexLow()
                    && $match->getRangeRight()->getIndexLow() > $currentWindow->getRangeRight()->getIndexLow()
                ) {
                    // Don't worry, ranges are immutable, and using a setter will return a new object
                    $subRangesToTheLeft = new RangePair(
                        $currentWindow->getRangeLeft()->setIndexHigh($match->getRangeLeft()->getIndexLow() - 1),
                        $currentWindow->getRangeRight()->setIndexHigh($match->getRangeRight()->getIndexLow() - 1)
                    );
                    $stack[] = $subRangesToTheLeft;
                }

                // If both sequences have elements to the right of the matched block, push it to the stack
                if ($match->getRangeLeft()->getIndexHigh() < $currentWindow->getRangeLeft()->getIndexHigh()
                    && $match->getRangeRight()->getIndexHigh() < $currentWindow->getRangeRight()->getIndexHigh()
                ) {
                    $subRangesToTheRight = new RangePair(
                        $currentWindow->getRangeLeft()->setIndexLow($match->getRangeLeft()->getIndexHigh() + 1),
                        $currentWindow->getRangeRight()->setIndexLow($match->getRangeRight()->getIndexHigh() + 1)
                    );
                    $stack[] = $subRangesToTheRight;
                }
            }
        }

        // Sort matching blocks
        $this->sortMatchingBlocks($matchingBlocks);

        // The difflib.py implementation features an adjacent block collapsing routine here. Seems like since we don't
        // use the concept of junk in our implementation, that will never be a problem, because LCCSFinder will always
        // expand itself to the largest possible match, and the lines around matched blocks will always be non-equal

        return $matchingBlocks;
    }

    /**
     * Calculate sequence of opcodes for given matching blocks
     *
     * @param RangePair[] $matchingBlocks An array of range pairs describing matches in sequences,
     *                                    <b>sorted</b> by their appearance in the sequences (otherwise will misbehave!)
     */
    public function getOpCodes(array $matchingBlocks)
    {
        // todo;
    }

    /**
     * Sort matching blocks by their order in the sequence
     *
     * @param array $matchingBlocks
     */
    private function sortMatchingBlocks(array &$matchingBlocks)
    {
        usort(
            $matchingBlocks,
            function (RangePair $a, RangePair $b) {
                return $a->getRangeLeft()->getIndexLow() - $b->getRangeLeft()->getIndexLow();
            }
        );
    }
} 
