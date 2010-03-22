<?php

class preplusplus extends instruction {
    protected $variable = null;
    protected $operateur = null;
    
    function __construct($variable) {
        parent::__construct(array());
        
        $operateur = new token_traite($variable[0]);
        $operateur->replace($variable[0]);
            
        $this->operateur = $operateur;
        $this->variable  = $variable[1];
    }

    function __toString() {
        return __CLASS__." ".$this->variable.$this->operateur;
    }

    function getVariable() {
        return $this->variable;
    }

    function getOperateur() {
        return $this->operateur;
    }

    function neutralise() {
        $this->variable->detach();
        $this->operateur->detach();
    }

    function getRegex(){
        return array('preplusplus_regex'
                    );
    }

}

?>