<?php

class _try extends instruction {
    protected $block = null;
    protected $catch = null;
    
    function __construct($expression = null) {
        parent::__construct(array());
        
        $this->block = $expression[0];
        unset($expression[0]);
        $this->catch = array_values($expression);
    }

    function __toString() {
        return __CLASS__." try { ".$this->block." } ";
    }

    function getBlock() {
        return $this->block;
    }

    function getCatch() {
        return $this->catch;
    }

    function neutralise() {
        $this->block->detach();
        foreach($this->catch as $e) {
            $e->detach();
        }
    }

    function getRegex(){
        return array('try_normal_regex',
                    );
    }

}

?>