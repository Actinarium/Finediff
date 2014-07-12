<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 12.07.14
 * Time: 18:02
 */

namespace Actinarium\Finediff\Core;


interface Searchable
{
    /**
     * Finds indices where given data string appears
     *
     * @param string $data
     *
     * @return int[]
     */
    public function findOccurrences($data);
} 
