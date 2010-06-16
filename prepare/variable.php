<?php

class variable extends token {
    protected $nom = null;

    function __construct($entree = null) {
        parent::__construct(array());
        
        if (empty($entree)) {
            $this->nom = null;
        } elseif(count($entree) == 1) {
            if ($entree[0]->checkClass('Token')) {
                $this->nom = $entree[0]->getCode();
            } else {
                $this->nom = $entree[0];
            }
        } elseif(count($entree) == 2) {
          $this->nom = $entree[1];
          $this->code = $this->nom->getCode();
        }
    }

    function __toString() {
        return __CLASS__." ".$this->nom;
    }
    
    function getNom() {
        return $this->nom;
    }
    
    function neutralise() {
        if (is_object($this->nom)) {
            $this->nom->detach();
        }
    }

    function getRegex(){
        return array('variable_regex',
                     'variable_accolade_regex',
                     'variable_accoladeseparee_regex',
                     'variable_variable_regex',
                     );
    }
}

?>