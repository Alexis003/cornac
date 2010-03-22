<?php

class xxxxxx extends instruction {
    protected $condition = null;
    protected $then = null;
    protected $else = null;
    
    function __construct($condition, $then, $else) {
        parent::__construct(array());
        
        $this->condition = $condition;
        $this->then = $then;
        $this->else = $else;
        
    }

    function __toString() {
        return __CLASS__." if (".$this->condition.") then ".$this->then." else ".$this->else;
    }

    function getCondition() {
        return $this->condition;
    }

    function getThen() {
        return $this->then;
    }

    function getElse() {
        return $this->else;
    }
    
    function getRegex() {
        return array();
    }

}

?>