<?php

class _while extends instruction {
    protected $expression = null;
    
    function __construct($expression = null) {
        parent::__construct(array());
        
        // @todo check count of expression
        $this->condition = $expression[0];
        $this->block = $expression[1];
    }

    function __toString() {
        return __CLASS__." ".$this->code;
    }

    function getBlock() {
        return $this->block;
    }

    function getCondition() {
        return $this->condition;
    }

    function neutralise() {
        $this->condition->detach();
        $this->block->detach();
    }

    function getRegex(){
        return array('while_block_regex',
                     'while_noblock_regex',
                     'while_alternative_regex',
                     'dowhile_simples_regex',
                    );
    }

}

?>