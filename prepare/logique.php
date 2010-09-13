<?php

class logique extends instruction {
    protected $droite = null;
    protected $operateur = null;
    protected $gauche = null;
    
    function __construct($expression) {
        parent::__construct(array());
        
        if (is_array($expression)) {
            $this->droite = $expression[0];
            $this->operateur = $this->make_token_traite($expression[1]);
            $this->gauche = $expression[2];
        } else {
            $this->stop_on_error("Must receive an array as argument : ".count($expression)." received\n");
        }
    }

    function __toString() {
        return __CLASS__." ".$this->droite." ".$this->operateur." ".$this->gauche;
    }

    function getDroite() {
        return $this->droite;
    }

    function getOperateur() {
        return $this->operateur;
    }

    function getGauche() {
        return $this->gauche;
    }

    function neutralise() {
       $this->droite->detach();
       $this->operateur->detach();
       $this->gauche->detach();
    }

    static function getRegex() {
        return array('logique_regex');
    }
}

?>