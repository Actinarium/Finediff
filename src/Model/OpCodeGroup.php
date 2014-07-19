<?php
/**
 * @author Actine <actine@actinarium.com>
 * Date: 15.07.14
 * Time: 1:48
 */

namespace Actinarium\Finediff\Model;


class OpCodeGroup {

    /** @var  int */
    private $context;
    /** @var  OpCode[] */
    private $opcodes;

    /**
     * @param int $context
     *
     * @return OpCodeGroup self-reference
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return int
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param \Actinarium\Finediff\Model\OpCode[] $opcodes


*
*@return OpCodeGroup self-reference
     */
    public function setOpcodes($opcodes)
    {
        $this->opcodes = $opcodes;
        return $this;
    }

    /**
     * @return \Actinarium\Finediff\Model\OpCode[]
     */
    public function getOpcodes()
    {
        return $this->opcodes;
    }

}
