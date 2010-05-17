<?php

class comparaison extends instruction {
    protected $droite = null;
    protected $operateur = null;
    protected $gauche = null;
    
    function __construct($entree) {
        parent::__construct(array());
        
        if (is_array($entree) && count($entree) == 3) {
            $this->droite = $entree[0];
            $this->operateur = $this->make_token_traite($entree[1]);
            $this->gauche = $entree[2];
        } else {
            die("Nombre d'arguments pour ".__CLASS__." incompris : ".count($entree)." au lieu de 3\n");
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
        return array('comparaison_regex');
    }
}

?>