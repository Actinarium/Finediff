<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 12.07.14
 * Time: 20:29
 */

namespace Actinarium\Finediff\Comparison;


class DefaultStringComparisonStrategy implements StringComparisonStrategy
{
    /**
     * Verify if two given strings are equal by simply comparing them using === operator (the fastest way)
     *
     * @param string $stringA
     * @param string $stringB
     *
     * @return boolean
     */
    public function areEqual($stringA, $stringB)
    {
        return $stringA === $stringB;
    }
}
