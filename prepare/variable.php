<?php

class variable extends token {
    protected $nom = null;

    function __construct($expression = null) {
        parent::__construct(array());
        
        if (empty($expression)) {
            $this->nom = null;
        } elseif(count($expression) == 1) {
          $this->nom = $expression[0];
        } elseif(count($expression) == 2) {
          $this->nom = $expression[1];
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
        //  $this->code = $this->nom->getCode();
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