<?php

class _dowhile extends instruction {
    protected $expression = null;
    
    function __construct($entree = null) {
        parent::__construct(array());
        
        $this->block = $entree[0];
        $this->condition = $entree[1];
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
        return array('dowhile_block_regex',
                     'dowhile_apres_regex',

                    );
    }

}

?>