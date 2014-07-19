<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 12.07.14
 * Time: 20:28
 */

namespace Actinarium\Finediff\Comparison;


interface StringComparisonStrategy
{
    /**
     * Verify if two given strings are equal
     *
     * @param string $stringA
     * @param string $stringB
     *
     * @return boolean
     */
    public function areEqual($stringA, $stringB);
} 
