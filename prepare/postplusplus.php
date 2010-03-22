<?php

class postplusplus extends instruction {
    protected $variable = null;
    protected $operateur = null;
    
    function __construct($variable) {
        parent::__construct(array());

        $operateur = new token_traite($variable[1]);
        $operateur->replace($variable[1]);
            
        $this->operateur = $operateur;
        $this->variable  = $variable[0];
    }

    function __toString() {
        return __CLASS__." ".$this->operateur.$this->variable;
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
        return array('postplusplus_regex'
                    );
    }

}

?>