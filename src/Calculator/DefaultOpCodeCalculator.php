<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 13.07.14
 * Time: 15:53
 */

namespace Actinarium\Finediff\Calculator;


use Actinarium\Finediff\MatchFinder\MatchFinder;
use Actinarium\Finediff\Model\BlocksMetadata;
use Actinarium\Finediff\Model\OpCode;
use Actinarium\Finediff\Model\Range;
use Actinarium\Finediff\Model\RangePair;
use Actinarium\Finediff\Sequence\IndexedSequence;
use Actinarium\Finediff\Sequence\Sequence;

class DefaultOpCodeCalculator implements OpCodeCalculator
{
    /** @var  MatchFinder */
    private $matchFinder;

    /**
     * @param \Actinarium\Finediff\MatchFinder\MatchFinder $matchFinder Implementation of longest common contiguous sub-sequence finder (default or
     *                                                                  custom)
     */
    public function __construct(MatchFinder $matchFinder)
    {
        $this->matchFinder = $matchFinder;
    }

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
    public function getOpCodes(IndexedSequence $base, Sequence $test)
    {
        // Initialize pointers to store last matching block ends, and an array for opcodes
        $pointerLeft = 0;
        $pointerRight = 0;
        $opCodes = array();

        $blocksMetadata = $this->getMatchingBlocks($base, $test);
        $matchingBlocks = & $blocksMetadata->getMatchingBlocks();

        foreach ($matchingBlocks as &$block) {
            $matchOpCode = new OpCode(
                $block->getRangeLeft(),
                $block->getRangeRight(),
                OpCode::EQUAL
            );

            // Check for the opcode between the last and current matching blocks
            $extraOpCode = $this->getOpCodeBefore($block, $pointerLeft, $pointerRight);
            if ($extraOpCode !== null) {
                $opCodes[] = $extraOpCode;
            }

            $opCodes[] = $matchOpCode;

            // Move the pointers to the next after current match
            $pointerLeft = $block->getRangeLeft()->getTo();
            $pointerRight = $block->getRangeRight()->getTo();
        }

        // Check whether there's a block after the last matching block before the end of sequences
        $bogusPair = new RangePair(
            new Range($blocksMetadata->getLengthLeft(), null),
            new Range($blocksMetadata->getLengthRight(), null)
        );
        $lastOpCode = $this->getOpCodeBefore($bogusPair, $pointerLeft, $pointerRight);
        if ($lastOpCode !== null) {
            $opCodes[] = $lastOpCode;
        }

        return $opCodes;
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
     * @return BlocksMetadata Range pairs for blocks that matched in both sequences, sorted by their appearance order.
     *                        Left range always corresponds to base, right range corresponds to test
     */
    private function getMatchingBlocks(IndexedSequence $base, Sequence $test)
    {
        $fullRanges = new RangePair(
            new Range(0, $base->getLength()),
            new Range(0, $test->getLength())
        );

        // Initialize empty array for matching blocks
        /** @var RangePair[] $matchingBlocks */
        $matchingBlocks = array();

        // Instead of finding matches recursively, use the stack to store blocks between matches.
        $stack = array($fullRanges);
        while (!empty($stack)) {
            /** @var RangePair $currentWindow */
            $currentWindow = array_pop($stack);
            $match = $this->matchFinder->find($base, $test, $currentWindow);
            if ($match !== null) {
                $matchingBlocks[] = $match;

                // If both sequences have elements to the left of the matched block, push it to the stack
                if ($match->getRangeLeft()->getFrom() > $currentWindow->getRangeLeft()->getFrom()
                    && $match->getRangeRight()->getFrom() > $currentWindow->getRangeRight()->getFrom()
                ) {
                    // Don't worry, ranges are immutable, and using a setter will return a new object
                    $subRangesToTheLeft = new RangePair(
                        $currentWindow->getRangeLeft()->setTo($match->getRangeLeft()->getFrom()),
                        $currentWindow->getRangeRight()->setTo($match->getRangeRight()->getFrom())
                    );
                    $stack[] = $subRangesToTheLeft;
                }

                // If both sequences have elements to the right of the matched block, push it to the stack
                if ($match->getRangeLeft()->getTo() < $currentWindow->getRangeLeft()->getTo()
                    && $match->getRangeRight()->getTo() < $currentWindow->getRangeRight()->getTo()
                ) {
                    $subRangesToTheRight = new RangePair(
                        $currentWindow->getRangeLeft()->setFrom($match->getRangeLeft()->getTo()),
                        $currentWindow->getRangeRight()->setFrom($match->getRangeRight()->getTo())
                    );
                    $stack[] = $subRangesToTheRight;
                }
            }
        }

        // Sort matching blocks
        $this->sortMatchingBlocks($matchingBlocks);

        // The difflib.py implementation features an adjacent block collapsing routine here. Seems like since we don't
        // use the concept of junk in our implementation, that will never be a problem, because MatchFinder will always
        // expand itself to the largest possible match, and the lines around matched blocks will always be non-equal

        return new BlocksMetadata($matchingBlocks, $base->getLength(), $test->getLength());
    }

    /**
     * Sort matching blocks by their order in the sequence
     *
     * @param RangePair[] $matchingBlocks
     */
    private function sortMatchingBlocks(array &$matchingBlocks)
    {
        usort(
            $matchingBlocks,
            function (RangePair $a, RangePair $b) {
                return $a->getRangeLeft()->getFrom() - $b->getRangeLeft()->getFrom();
            }
        );
    }

    /**
     * Determine if there is a block between given block and a pair of pointers
     *
     * @param RangePair $block        Given (next) block
     * @param int       $pointerLeft  Pointer in the left sequence (index at element following the one from last match)
     * @param int       $pointerRight Pointer in the right sequence (index at element following the one from last match)
     *
     * @return OpCode|null
     */
    private function getOpCodeBefore(RangePair $block, &$pointerLeft, &$pointerRight)
    {
        $isGapInLeft = $block->getRangeLeft()->getFrom() > $pointerLeft;
        $isGapInRight = $block->getRangeRight()->getFrom() > $pointerRight;
        $extraOpCode = null;
        if ($isGapInLeft && $isGapInRight) {
            // If there were non-matching lines between matching blocks in both sequences - then it's replacement
            $extraOpCode = new OpCode(
                new Range($pointerLeft, $block->getRangeLeft()->getFrom()),
                new Range($pointerRight, $block->getRangeRight()->getFrom()),
                OpCode::REPLACE
            );
        } elseif ($isGapInLeft) {
            // If there was only gap in the left but no lines on the right, then that's a removal
            $extraOpCode = new OpCode(
                new Range($pointerLeft, $block->getRangeLeft()->getFrom()),
                new Range($pointerRight, $pointerRight),
                OpCode::DELETE
            );
        } elseif ($isGapInRight) {
            // If there was only gap in the right but no lines on the left, then that's an insertion
            $extraOpCode = new OpCode(
                new Range($pointerLeft, $pointerLeft),
                new Range($pointerRight, $block->getRangeRight()->getFrom()),
                OpCode::INSERT
            );
        }
        return $extraOpCode;
    }
} 
