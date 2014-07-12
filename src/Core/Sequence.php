<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 12.07.14
 * Time: 17:38
 */

namespace Actinarium\Finediff\Core;


interface Sequence
{
    /**
     * @return string[]
     */
    public function getSequence();

    /**
     * @return int
     */
    public function getLength();
}
