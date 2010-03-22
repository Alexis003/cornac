<?php

class affectation extends instruction {
    protected $_static    = null;
    protected $_visibility    = null;
    protected $droite    = null;
    protected $operateur = null;
    protected $gauche    = null;
    
    function __construct($entree) {
        parent::__construct(array());
        
        if (is_array($entree)) {
            while ($entree[0]->checkToken(array(T_PUBLIC, T_PRIVATE, T_PROTECTED, T_STATIC))) {
                if ($entree[0]->checkToken(array(T_PUBLIC, T_PRIVATE, T_PROTECTED))) {
                    $this->_visibility = $entree[0];
                } elseif ($entree[0]->checkToken(array(T_STATIC))) {
                    $this->_static = $entree[0];
                }

                unset($entree[0]);
                $entree = array_values($entree);
            }

            if (count($entree) != 3) {
                die("Affectation avec un nombre de valeurs inapproprié\n");
            }

            $this->droite = $entree[0];
            
            $operateur = new token_traite($entree[1]);
            $operateur->replace($entree[1]);
                
            $this->operateur = $operateur;
    
            $this->gauche = $entree[2];
        } else {
            die(__CLASS__." a recu des arguments bizarres dans ".__METHOD__);
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

    function getVisibility() {
        return $this->_visibility;
    }

    function getStatic() {
        return $this->_static;
    }

    function getToken() {
        return 0;
    }

    function neutralise() {
        if (!is_null($this->_visibility)) {
            $this->_visibility->detach();
        }
        if (!is_null($this->_static)) {
            $this->_static->detach();
        }
        $this->droite->detach();
        $this->operateur->detach();
        $this->gauche->detach();
    }

    function getRegex(){
        return array('affectation_normal_regex', 
                     'affectation_avecpointvirgule_regex', 
                     'affectation_list_regex',
                    );
    }
}

?>