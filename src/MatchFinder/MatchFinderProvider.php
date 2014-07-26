<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 27.07.14
 * Time: 1:12
 */

namespace Actinarium\Finediff\MatchFinder;


interface MatchFinderProvider
{
    /**
     * Get a match finder (singleton service or instance, depending on functionality)
     *
     * @return MatchFinder
     */
    public function getMatchFinder();
} 
