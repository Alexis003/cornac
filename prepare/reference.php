<?php

class reference extends instruction {
    private $expression = null;
    
    function __construct($expression) {
        parent::__construct(array());
  
        $this->expression = $expression[0];
    }

    function __toString() {
        return __CLASS__." &".$this->expression;
    }

    function getExpression() {
        return $this->expression;
    }

    function neutralise() {
        $this->expression->detach();
    }

    function getRegex(){
        return array('reference_normal_regex'
                    );
    }

}

?>