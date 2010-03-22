<?php

class arginit extends instruction {
    protected $variable = array();
    protected $valeur = null;

    function __construct($entree) {
        parent::__construct(array());

        $this->variable = $entree[0];
        $this->valeur = $entree[1];
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
//    'arginit_typehint_regex',
);
    }
}

?>