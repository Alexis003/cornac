<?php

class cast extends instruction {
    protected $cast = null;
    protected $expression = null;
    
    function __construct($expression) {
        parent::__construct(array());
        
        $cast = new token_traite($expression[0]);
        $cast->replace($expression[0]);

        $this->cast = $cast;
        $this->expression = $expression[1];
    }

    function __toString() {
        return __CLASS__." ".$this->code;
    }

    function getCast() {
        return $this->cast;
    }

    function getExpression() {
        return $this->expression;
    }

    function neutralise() {
        $this->cast->detach();
        $this->expression->detach();
    }

    function getRegex(){
        return array('cast_normal_regex'
                    );
    }

}

?>