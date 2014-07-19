<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 12.07.14
 * Time: 18:02
 */

namespace Actinarium\Finediff\Sequence;


use int;

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

    /**
     * Returns true if all lines were indexed, and false if index is partial (e.g. frequent items removed like in
     * difflib.py)
     *
     * @return bool
     */
    public function isIndexComplete();
} 
