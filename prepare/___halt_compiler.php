<?php

class ___halt_compiler extends instruction {

    function __construct($expression = null) {
        parent::__construct(array());
    }
    
    function __toString() {
         $retour = __CLASS__;
         return $retour;
    }

    function neutralise() {
    }

    function getRegex() {
        return array(
    '___halt_compiler_regex',
                    );
    }
}

?>