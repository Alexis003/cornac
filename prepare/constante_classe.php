<?php

class constante_classe extends token {
    protected $name = null;
    protected $constante = null;
    
    function __construct($entree) {
        parent::__construct();
        
        if (is_array($entree) && count($entree) == 2) {
            $this->name = $entree[0];
            $this->constante = $entree[1];
        } else {
            die('Appel de constant avec deux arguments? '. join(', ',func_get_args()));
        }
    }

    function getName() {  
        return $this->name;
    }

    function getConstante() {
        return $this->constante;
    }

    function neutralise() {
        $this->name->detach();
        $this->constante->detach();
    }

    function __toString() {
        return __CLASS__." ".$this->name."::".$this->constante;
    }

    function getRegex(){
        return array('constante_classe_regex',
                     );
    }

}

?>