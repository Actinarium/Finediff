<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 12.07.14
 * Time: 20:29
 */

namespace Actinarium\Finediff\Impl;


use Actinarium\Finediff\Core\StringComparisonStrategy;

class DefaultStringComparisonStrategy implements StringComparisonStrategy
{

    /**
     * Verify if two given strings are equal by simply comparing them byte by byte. The method relies on the fact that
     * two provided parameters must be strings, otherwise the result is unpredictable (type juggling may occur)
     *
     * @param string $stringA
     * @param string $stringB
     *
     * @return boolean
     */
    public function areEqual($stringA, $stringB)
    {
        return strcmp($stringA, $stringB) === 0;
    }
}
