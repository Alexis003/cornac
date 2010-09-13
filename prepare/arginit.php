<?php

class arginit extends instruction {
    protected $variable = array();
    protected $valeur = null;

    function __construct($expression) {
        parent::__construct(array());

        $this->variable = $expression[0];
        $this->valeur = $expression[1];
    }
    
    function __toString() {
        return __CLASS__." ".$this->variable." = ".$this->valeur." ";
    }

    function getVariable() {
        return $this->variable;
    }

    function getValeur() {
        return $this->valeur;
    }

    function neutralise() {
        $this->variable->detach();
        $this->valeur->detach();
    }

    function getRegex() {
        return array(
    'arginit_literal_regex',
    'arginit_array_regex',
);
    }
}

?>