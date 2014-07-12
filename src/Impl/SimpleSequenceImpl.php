<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 12.07.14
 * Time: 18:02
 */

namespace Actinarium\Finediff\Impl;


use Actinarium\Finediff\Core\IndexedSequence;

class SimpleSequenceImpl implements IndexedSequence
{
    /** @var  string[] */
    protected $data;
    /** @var  null|int */
    protected $length;
    /** @var  null|array[] */
    protected $dictionary;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Finds indices where given data string appears
     *
     * @param string $data
     *
     * @return int[]|null  A sorted array of indices having given data, or null if data is not found
     */
    public function findOccurrences($data)
    {
        if ($this->dictionary === null) {
            $this->fillDictionary();
        }
        return isset($this->dictionary[$data]) ? $this->dictionary[$data] : null;
    }

    /**
     * @return string[]
     */
    public function &getSequence()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        if ($this->length === null) {
            $this->length = count($this->data);
        }
        return $this->length;
    }

    protected function fillDictionary()
    {
        foreach ($this->data as $index => $line) {
            // It's assumed that lines are never null, otherwise isset won't work
            if (isset($this->dictionary[$line])) {
                // Add to existing record
                $this->dictionary[$line][] = $index;
            } else {
                // Create a new record with one element
                $this->dictionary[$line] = array($index);
            }
        }
    }
}
