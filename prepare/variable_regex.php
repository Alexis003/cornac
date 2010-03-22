<?php

class variable_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_VARIABLE,T_STRING_VARNAME);
    }
    
    function check($t) {
    
        if (!$t->hasPrev() ) { return false; }
        if (!$t->hasNext() ) { return false; }

        if ($t->checkToken(array(T_VARIABLE,T_STRING_VARNAME))
            ) {

            $this->args   = array(0);
            $this->remove = array();

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>