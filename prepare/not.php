<?php

class not extends instruction {
    protected $expression = null;
    
    function __construct($expression) {
        parent::__construct(array());
        
        $this->expression = $expression[0];
    }

    function __toString() {
        return __CLASS__." ".$this->code;
    }

    function neutralise() {
        $this->expression->detach();
    }
    
    static function getRegex() {
        return array('not_regex');
    }

}

?>