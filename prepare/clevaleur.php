<?php

class clevaleur extends instruction {
    protected $expression = null;
    
    function __construct($expression = null) {
        parent::__construct(array());
        
        $this->cle = $expression[0];
        $this->valeur = $expression[1];
    }

    function __toString() {
        return __CLASS__." ".$this->cle." => ".$this->valeur;
    }

    function getCle() {
        return $this->cle;
    }

    function getValeur() {
        return $this->valeur;
    }

    function neutralise() {
        $this->cle->detach();
        $this->valeur->detach();
    }

    function getRegex(){
        return array('clevaleur_regex',
                    );
    }

}

?>