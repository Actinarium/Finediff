<?php

namespace Actinarium\Finediff\Test;

/**
 * @author Actine <actine@actinarium.com>
 * Date: 14.07.14
 * Time: 4:20
 */

include "../vendor/autoload.php";

use Actinarium\Finediff\Calculator\DefaultOpCodeCalculator;
use Actinarium\Finediff\Comparison\DefaultStringComparisonStrategy;
use Actinarium\Finediff\MatchFinder\CachingMatchFinderProvider;
use Actinarium\Finediff\MatchFinder\DefaultMatchFinder;
use Actinarium\Finediff\MatchFinder\DefaultMatchFinderProvider;
use Actinarium\Finediff\Sequence\SimpleSequenceImpl;

$strategy = new DefaultStringComparisonStrategy();
$finderProvider1 = new DefaultMatchFinderProvider($strategy);
$finderProvider2 = new CachingMatchFinderProvider($strategy);
$calculator1 = new DefaultOpCodeCalculator($finderProvider1);
$calculator2 = new DefaultOpCodeCalculator($finderProvider2);

$a = explode("\r\n", file_get_contents(dirname(__FILE__) . '/aa.txt'));
$b = explode("\r\n", file_get_contents(dirname(__FILE__) . '/bb.txt'));

$sequence1 = new SimpleSequenceImpl($a);
$sequence2 = new SimpleSequenceImpl($b);

$opcodes1 = $calculator1->getOpCodes($sequence1, $sequence2);
$opcodes2 = $calculator2->getOpCodes($sequence1, $sequence2);

echo "Non-caching" . PHP_EOL;
for ($j = 0; $j < 5; $j++) {
    $before = microtime(true);
    for ($i = 0; $i < 10; $i++) {
        $calculator1->getOpCodes($sequence1, $sequence2);
    }
    $after = microtime(true);
    echo $after-$before . PHP_EOL;
}
echo "Caching" . PHP_EOL;
for ($j = 0; $j < 5; $j++) {
    $before = microtime(true);
    for ($i = 0; $i < 10; $i++) {
        $calculator2->getOpCodes($sequence1, $sequence2);
    }
    $after = microtime(true);
    echo $after-$before . PHP_EOL;
}

die;
