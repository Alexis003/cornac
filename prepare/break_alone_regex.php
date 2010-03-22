<?php

class break_alone_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }
    
    function getTokens() {
        return array(T_BREAK);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }

        if ($t->checkToken(T_BREAK) &&
            $t->getNext()->checkCode(';')
            ) {

            $this->args = array(0 );
            $this->remove = array();

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>