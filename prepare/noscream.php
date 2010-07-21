<?php

class noscream extends instruction {
    protected $expression = null;
    
    function __construct($expression) {
        parent::__construct(array());
        
        $this->expression = $expression[0];
        $this->setLine($this->expression->getLine());
    }

    function __toString() {
        return __CLASS__." ".$this->code;
    }

    function getExpression() {
        return $this->expression;
    }

    function neutralise() {
        $this->expression->detach();
    }

    function getRegex(){
        return array('noscream_normal_regex'
                    );
    }

}

?>