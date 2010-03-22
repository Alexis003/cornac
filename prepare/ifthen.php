<?php

class ifthen extends instruction {
    protected $condition = array();
    protected $then = array();
    protected $else = null;
    

    function __construct($condition) {
        parent::__construct(array());
        
        while(count($condition) >= 2) {
            $this->condition[] = array_shift($condition);
            $this->then[]      = array_shift($condition);
        }
        if (count($condition) == 1) {
            $this->else = array_shift($condition);
        }
    }
    
    function add($condition, $then) {
        $this->condition[] = $condition;
        $this->then[] = $then;
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

    function getToken() {
        return 0;
    }

    function neutralise() {
        foreach($this->condition as &$condition) {
            $condition->detach();
        }
        foreach($this->then as &$then) {
            $then->detach();
        }
        if (!is_null($this->else)) {
            $this->else->detach();
        }
    }

    function getRegex() {
        return array(
    'ifthen_block_regex',
    'ifthen_blockelseblock_regex',
    'ifthenelse_multiples_regex',
    'ifthenelse_simples_regex',
    'ifthenelseif_simples_regex',
    'ifthenelseif_sequence_regex',
    'ifthenelse_sequence_regex',

    'ifthenelseif_alternative_regex',
    'ifthenelse_alternativeblock_regex',
    'ifthenelse_alternative_regex',
    
    'ifthenelse_vides_regex',
    );

    }
}

?>