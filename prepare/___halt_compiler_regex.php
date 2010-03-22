<?php

class ___halt_compiler_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_HALT_COMPILER);
    }
 
    
    function check($t) {
        if (!$t->hasNext(2)) { return false; }
        
        if ($t->getNext()->checkNotOperateur('(')) { return false; }
        if ($t->getNext(1)->checkNotOperateur(')')) { return false; }

        $this->args = array();
        $this->remove = array(1, 2);

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>