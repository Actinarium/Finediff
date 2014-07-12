<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 12.07.14
 * Time: 18:02
 */

namespace Actinarium\Finediff\Impl;


use Actinarium\Finediff\Core\Searchable;
use Actinarium\Finediff\Core\Sequence;

class SimpleSequenceImpl implements Searchable, Sequence
{
    /** @var  string[] */
    protected $data;
    /** @var  int|null */
    protected $length;
    /** @var  array[] */
    protected $dictionary;

    public function __construct(array $data = null)
    {
        $this->data = $data;
    }

    /**
     * Finds indices where given data string appears
     *
     * @param string $data
     *
     * @return int[]
     */
    public function findOccurrences($data)
    {
        if ($this->dictionary === null) {
            $this->fillDictionary();
        }
        return array_key_exists($data, $this->dictionary) ? $this->dictionary[$data] : array();
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

    /**
     *
     */
    protected function fillDictionary() { }
}
