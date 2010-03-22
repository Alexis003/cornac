<?php

class constante_magique_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_FILE, T_CLASS_C, T_LINE, T_METHOD_C, T_FUNC_C);
    }
    
    function check($t) {

        if ($t->checkToken(array(T_FILE, T_CLASS_C, T_LINE, T_METHOD_C, T_FUNC_C)) ) {

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>