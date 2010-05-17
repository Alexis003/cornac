<?php

class postplusplus extends instruction {
    protected $variable = null;
    protected $operateur = null;
    
    function __construct($entree) {
        parent::__construct(array());
            
        $this->variable  = $entree[0];
        $this->operateur = $this->make_token_traite($entree[1]);
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