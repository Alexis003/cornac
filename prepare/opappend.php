<?php

class opappend extends instruction {
    protected $variable = null;
    
    function __construct($variable) {
        parent::__construct(array());
        
        $this->variable = $variable[0];
        $this->setLine($this->variable->getLine());
    }

    function __toString() {
        return __CLASS__." ".$this->code."[]";
    }

    function getVariable() {
        return $this->variable;
    }

    function neutralise() {
        $this->variable->detach();
    }

    function getRegex(){
        return array('opappend_regex'
                    );
    }

}

?>