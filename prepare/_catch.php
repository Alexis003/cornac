<?php

class _catch extends instruction {
    protected $exception = null;
    protected $variable = null;
    protected $block = null;
    
    function __construct($entree = null) {
        parent::__construct(array());
        
        $this->block = array_pop($entree);
        if (count($entree) == 2) {
            $this->exception = $this->make_token_traite($entree[0]);
            $this->variable  = $entree[1];
        } else {
            $this->stop_on_error("Unexpected number of arguments received : (".count($entree)." instead of 3) in ".__METHOD__);
        }
    }

    function __toString() {
        return __CLASS__." (".$this->exception." ".$this->variable.") ";
    }

    function getException() {
        return $this->exception;
    }

    function getVariable() {
        return $this->variable;
    }

    function getBlock() {
        return $this->block;
    }

    function neutralise() {
        $this->exception->detach();
        $this->variable->detach();
        $this->block->detach();
    }

    function getRegex(){
        return array('catch_normal_regex',
                    );
    }

}

?>