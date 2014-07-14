<?php

namespace Actinarium\Finediff\Test;

/**
 * @author Actine <actine@actinarium.com>
 * Date: 14.07.14
 * Time: 4:20
 */

include "../vendor/autoload.php";

use Actinarium\Finediff\Impl\DefaultLCCSFinder;
use Actinarium\Finediff\Impl\DefaultOpCodeCalculator;
use Actinarium\Finediff\Impl\DefaultStringComparisonStrategy;
use Actinarium\Finediff\Impl\SimpleSequenceImpl;

$strategy = new DefaultStringComparisonStrategy();
$finder = new DefaultLCCSFinder($strategy);
$calculator = new DefaultOpCodeCalculator($finder);

$a = explode("\r\n", file_get_contents(dirname(__FILE__).'/a.txt'));
$b = explode("\r\n", file_get_contents(dirname(__FILE__).'/b.txt'));

$sequence1 = new SimpleSequenceImpl($a);
$sequence2 = new SimpleSequenceImpl($b);

$blocks = $calculator->getMatchingBlocks($sequence1, $sequence2);
$opcodes = $calculator->getOpCodes($blocks);

sleep(1);
