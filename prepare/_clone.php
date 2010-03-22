<?php

class _clone extends instruction {
    protected $expression = null;
    
    function __construct($expression) {
        parent::__construct(array());
        
        $this->expression = $expression[0];
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
        return array('clone_normal_regex',
                     'clone_parentheses_regex',
                    );
    }

}

?>