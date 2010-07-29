<?php

class _new extends instruction {
    protected $classe = null;
    protected $expression = null;
    
    function __construct($entree) {
        parent::__construct(array());
        
        $constructeur = $entree[0];
        if (get_class($constructeur) == 'functioncall') {
            $this->classe = $constructeur->getFunction();
            $this->args = $constructeur->getargs();
        } elseif (get_class($constructeur) == 'method') {
            $this->classe = $constructeur;
            if (!isset($entree[1])) {
                $this->args = new arglist();
            } else {
                $this->args = $entree[1];
            }
        } elseif (get_class($constructeur) == 'constante') {
            $this->classe =  new token_traite($constructeur->getName());
            if (!isset($entree[1])) {
                $this->args = new arglist();
            } else {
                $this->args = $entree[1];
            }
        } elseif ($constructeur->checkClass(array('variable','tableau','property','property_static','method_static'))) {
            $this->classe = $constructeur;

            if (!isset($entree[1])) {
                $this->args = new arglist();
            } else {
                $this->args = $entree[1];
            }
        } else {
            $this->stop_on_error("Unexpected class received : '".get_class($constructeur)."' in ".__METHOD__);
        }
    }

    function __toString() {
        return __CLASS__." ".$this->expression;
    }

    function getClasse() {
        return $this->classe;
    }

    function getArgs() {
        return $this->args;
    }

    function neutralise() {
        $this->classe->detach();
        $this->args->detach();
    }

    function getRegex(){
        return array('new_normal_regex',
                     'new_single_regex',
                     'new_variable_regex',
                    );
    }

}

?>