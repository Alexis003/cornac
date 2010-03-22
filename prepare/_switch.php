<?php

class _switch extends instruction {
    protected $expression = null;
    
    function __construct($expression = null) {
        parent::__construct(array());
        
        $this->operande = $expression[0];
        $this->block = $expression[1];
    }

    function __toString() {
        return __CLASS__." ".$this->code;
    }

    function getOperande() {
        return $this->operande;
    }

    function getBlock() {
        return $this->block;
    }

    function neutralise() {
        $this->operande->detach();
        $this->block->detach();
    }

    function getRegex(){
        return array('switch_simple_regex',
                     'switch_alternative_regex',
                    );
    }

}

?>