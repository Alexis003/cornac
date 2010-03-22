<?php

class method_static_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_DOUBLE_COLON);
    }
    
    function check($t) {
    
        if (!$t->hasPrev() ) { return false; }
        if (!$t->hasNext() ) { return false; }

        if ( $t->checkToken(T_DOUBLE_COLON) && 
            ($t->getPrev()->checkToken(array(T_STRING, T_STATIC)) || 
             $t->getPrev()->checkClass(array('variable','tableau'))) &&
             $t->getNext()->checkClass('functioncall')
           ) {

            $this->args   = array(-1, 1);
            $this->remove = array(-1, 1);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>