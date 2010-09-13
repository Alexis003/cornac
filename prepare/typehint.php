<?php

class typehint extends token {
    protected $type = null;
    protected $nom = null;

    function __construct($expression = null) {
        parent::__construct(array());
        
        if (count($expression) != 2) { 
            $this->stop_on_error("Number of argument is wrong");
        }
        
        $this->type = $this->make_token_traite($expression[0]);
        $this->nom = $expression[1];
    }

    function __toString() {
        return __CLASS__." ".$this->type." ".$this->nom;
    }
    
    function getNom() {
        return $this->nom;
    }

    function getType() {
        return $this->type;
    }

    function neutralise() {
        $this->type->detach();
        $this->nom->detach();
    }

    function getRegex(){
        return array('typehint_regex');
    }
}

?>