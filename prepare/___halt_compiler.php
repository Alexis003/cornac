<?php

class ___halt_compiler extends instruction {

    function __construct($expression = null) {
        parent::__construct(array());
    }
    
    function __toString() {
         $return = __CLASS__;
         return $return;
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