<?php

class operation extends instruction {
    protected $droite = null;
    protected $operation = null;
    protected $gauche = null;
    
    function __construct($expression) {
        parent::__construct(array());
        
        if (count($expression) == 3) {
            $this->droite = $expression[0];
            $this->operation = $this->make_token_traite($expression[1]);
            $this->gauche = $expression[2];
        } else {
            $this->stop_on_error("We shouldn't reach here");
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
    
    function getToken() { return Token::ANY_TOKEN; }
}

?>