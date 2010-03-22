<?php

class parentheses extends instruction {
    protected $contenu = null;
    
    function __construct($parentheses) {
        parent::__construct(array());
        
        $this->contenu = $parentheses[0];
    }

    function __toString() {
        return __CLASS__." (".$this->contenu.")";
    }

    function getContenu() {
        return $this->contenu;
    }

    function neutralise() {
        $this->contenu->detach();
    }

    function getRegex(){
        return array('parentheses_normal_regex',
                     );
    }

    function getCode() { return '';}
}

?>