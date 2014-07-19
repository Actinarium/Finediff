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
use Actinarium\Finediff\MatchFinder\DefaultMatchFinder;
use Actinarium\Finediff\Sequence\SimpleSequenceImpl;

$strategy = new DefaultStringComparisonStrategy();
$finder = new DefaultMatchFinder($strategy);
$calculator = new DefaultOpCodeCalculator($finder);

$a = explode("\r\n", file_get_contents(dirname(__FILE__) . '/aa.txt'));
$b = explode("\r\n", file_get_contents(dirname(__FILE__) . '/bb.txt'));

$sequence1 = new SimpleSequenceImpl($a);
$sequence2 = new SimpleSequenceImpl($b);

$opcodes = $calculator->getOpCodes($sequence1, $sequence2);

for ($j = 0; $j < 5; $j++) {
    $before = microtime(true);
    for ($i = 0; $i < 10; $i++) {
        $calculator->getOpCodes($sequence1, $sequence2);
//        $sequence1->findOccurrences("");
    }
    $after = microtime(true);
    echo $after-$before . PHP_EOL;
}

die;

for ($j = 0; $j < 5; $j++) {
    $before = microtime(true);
    for ($i = 99999; $i != 0; $i--) {
        array_slice($a, 1, -1);
    }
    $after = microtime(true);
    echo $after-$before . PHP_EOL;
}
