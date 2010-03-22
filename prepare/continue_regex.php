<?php

class continue_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_CONTINUE);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }

        if ($t->checkToken(T_CONTINUE) &&
            ($t->getNext()->checkCode(';') ||
             $t->getNext()->checkToken(T_CLOSE_TAG))
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