<?php

class preplusplus extends instruction {
    protected $variable = null;
    protected $operateur = null;
    
    function __construct($entree) {
        parent::__construct(array());
        
        $this->operateur = $this->make_token_traite($entree[0]);
        $this->variable  = $entree[1];
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