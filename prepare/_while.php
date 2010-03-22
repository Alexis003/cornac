<?php

class _while extends instruction {
    protected $expression = null;
    
    function __construct($entree = null) {
        parent::__construct(array());
        
        $this->condition = $entree[0];
        $this->block = $entree[1];
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