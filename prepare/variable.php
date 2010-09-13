<?php

class variable extends token {
    protected $nom = null;

    function __construct($expression = null) {
        parent::__construct(array());

        if (is_null($expression)) { // @note  coming from class tableau
            return ;
        }

        if (count($expression) == 1) {
            if ($expression[0]->checkClass('Token')) {
                $this->nom = $expression[0]->getCode();
            } else {
                $this->nom = $expression[0];
            }
            $this->setLine($expression[0]->getLine());
        } else {
          $this->nom = $expression[1];
          $this->code = $this->nom->getCode();
          $this->setLine($this->nom->getLine());
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