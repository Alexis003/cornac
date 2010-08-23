<?php

class constante extends instruction {
    
    function __construct() {
        parent::__construct(array());
        
    }

    function __toString() {
        return __CLASS__." ".$this->code;
    }

    function getName() {
        return $this->code;
    }

    function neutralise() {
    // @doc nothing to neutrize
    }

    function getRegex(){
        return array('constante_normal_regex',
                     'constante_magique_regex',
                    );
    }
    
}

?>