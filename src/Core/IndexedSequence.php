<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 12.07.14
 * Time: 18:02
 */

namespace Actinarium\Finediff\Core;


interface IndexedSequence extends Sequence
{
    /**
     * Finds indices where given data string appears.
     *
     * @param string $data
     *
     * @return int[]|null A sorted array of indices having given data, or null if data is not found
     */
    public function findOccurrences($data);
} 
