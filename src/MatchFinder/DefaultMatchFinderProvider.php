<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 27.07.14
 * Time: 1:13
 */

namespace Actinarium\Finediff\MatchFinder;


use Actinarium\Finediff\Comparison\StringComparisonStrategy;

class DefaultMatchFinderProvider implements MatchFinderProvider
{

    /** @var  DefaultMatchFinder */
    private $matchFinder;
    /** @var  StringComparisonStrategy */
    private $strategy;

    function __construct(StringComparisonStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * Get a match finder (singleton service or instance, depending on functionality)
     *
     * @return MatchFinder
     */
    public function getMatchFinder()
    {
        if ($this->matchFinder == null) {
            $this->matchFinder = new DefaultMatchFinder($this->strategy);
        }
        return $this->matchFinder;
    }
}
