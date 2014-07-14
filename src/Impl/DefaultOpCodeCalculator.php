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
     * @return BlocksMetadata Range pairs for blocks that matched in both sequences, sorted by their appearance order.
     *                        Left range always corresponds to base, right range corresponds to test
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

        return new BlocksMetadata($matchingBlocks, $base->getLength(), $test->getLength());
    }

    /**
     * Calculate sequence of opcodes for given matching blocks
     *
     * @param BlocksMetadata $blocksMetadata An array of range pairs describing matches in sequences, <b>sorted</b>
     *                                       by their appearance in the sequences (otherwise will misbehave!)
     *
     * @return OpCode[] Sequence of opcodes
     * @throws \LogicException In case of solar eclipse
     */
    public function getOpCodes(BlocksMetadata $blocksMetadata)
    {
        // Initialize pointers to store last matching block ends, and an array for opcodes
        $pointerLeft = 0;
        $pointerRight = 0;
        $opCodes = array();

        $matchingBlocks = & $blocksMetadata->getMatchingBlocks();

        foreach ($matchingBlocks as &$block) {
            $matchOpCode = new OpCode($block);
            $matchOpCode->setOperation(OpCode::EQUAL);

            // Check for the opcode between the last and current matching blocks
            $extraOpCode = $this->getOpCodeBefore($block, $pointerLeft, $pointerRight);
            if ($extraOpCode !== null) {
                $opCodes[] = $extraOpCode;
            }

            $opCodes[] = $matchOpCode;

            // Move the pointers to the next after current match
            $pointerLeft = $matchOpCode->getRangeLeft()->getIndexHigh() + 1;
            $pointerRight = $matchOpCode->getRangeRight()->getIndexHigh() + 1;
        }

        // Check whether there's a block after the last matching block before the end of sequences
        $bogusPair = new RangePair(
            new Range($blocksMetadata->getLengthLeft(), $blocksMetadata->getLengthLeft()),
            new Range($blocksMetadata->getLengthRight(), $blocksMetadata->getLengthRight())
        );
        $lastOpCode = $this->getOpCodeBefore($bogusPair, $pointerLeft, $pointerRight);
        if ($lastOpCode !== null) {
            $opCodes[] = $lastOpCode;
        }

        return $opCodes;
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

    /**
     * Determine if there is a block between given block and two pointers
     *
     * @param RangePair $block        Given (next) block
     * @param int       $pointerLeft  Pointer in the left sequence (index at element following the one from last match)
     * @param int       $pointerRight Pointer in the right sequence (index at element following the one from last match)
     *
     * @return OpCode|null
     */
    private function getOpCodeBefore(RangePair $block, &$pointerLeft, &$pointerRight)
    {
        $isGapInLeft = $block->getRangeLeft()->getIndexLow() > $pointerLeft;
        $isGapInRight = $block->getRangeRight()->getIndexLow() > $pointerRight;
        $extraOpCode = null;
        if ($isGapInLeft && $isGapInRight) {
            // If there were non-matching lines between matching blocks in both sequences - then it's replacement
            $extraOpCode = new OpCode(
                new RangePair(
                    new Range($pointerLeft, $block->getRangeLeft()->getIndexLow() - 1),
                    new Range($pointerRight, $block->getRangeRight()->getIndexLow() - 1)
                )
            );
            $extraOpCode->setOperation(OpCode::REPLACE);
        } elseif ($isGapInLeft) {
            // If there was only gap in the left but no lines on the right, then that's a removal
            $extraOpCode = new OpCode(
                new RangePair(
                    new Range($pointerLeft, $block->getRangeLeft()->getIndexLow() - 1),
                    new Range($pointerRight, $pointerRight)
                )
            );
            $extraOpCode->setOperation(OpCode::DELETE);
        } elseif ($isGapInRight) {
            // If there was only gap in the right but no lines on the left, then that's an insertion
            $extraOpCode = new OpCode(
                new RangePair(
                    new Range($pointerLeft, $pointerLeft),
                    new Range($pointerRight, $block->getRangeRight()->getIndexLow() - 1)
                )
            );
            $extraOpCode->setOperation(OpCode::DELETE);
        }
        return $extraOpCode;
    }
} 
