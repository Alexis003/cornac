<?php

class _default extends instruction {
    protected $expression = null;
    
    function __construct($expression = null) {
        parent::__construct(array());
        
        $this->block     = $expression[0];
    }

    function __toString() {
        return __CLASS__." ".$this->code;
    }

    function getBlock() {
        return $this->block;
    }

    function neutralise() {
        $this->block->detach();
    }

    function getRegex(){
        return array('default_block_regex',
                    );
    }

}

?>