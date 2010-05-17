<?php

class typehint extends token {
    protected $type = null;
    protected $nom = null;

    function __construct($entree = null) {
        parent::__construct(array());
        
        if (count($entree) != 2) { die("pb dans le nombre d'argument de ".__METHOD__);}
        
        $this->type = $this->make_token_traite($entree[0]);
        $this->nom = $entree[1];
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