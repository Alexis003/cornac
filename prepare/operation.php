<?php

class operation extends instruction {
    protected $droite = null;
    protected $operation = null;
    protected $gauche = null;
    
    function __construct($droite, $operation = null,  $gauche = null) {
        parent::__construct(array());
        
        if (is_array($droite)) {
            $this->droite = $droite[0];

            $operation = new token_traite($droite[1]);
            $operation->replace($droite[1]);
            
            $this->operation = $operation;
            $this->gauche = $droite[2];
        } else {
            $this->droite = $droite;

            $op = new token_traite($operation);
            $op->replace($operation);
            
            $this->operation = $op;
            $this->gauche = $gauche;
        }
    }

    function __toString() {
        return __CLASS__." ".$this->droite." ".$this->operation." ".$this->gauche;
    }

    function getDroite() {
        return $this->droite;
    }

    function getOperation() {
        return $this->operation;
    }

    function getGauche() {
        return $this->gauche;
    }

    function neutralise() {
       $this->droite->detach();
       $this->operation->detach();
       $this->gauche->detach();
    }

    function getRegex(){
        return array('operation_multiplication_regex',
                     'operation_addition_regex');
    }
    
    function getToken() { return 0; }
}

?>