<?php

class break_leveled_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_BREAK);
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->checkToken(T_BREAK) &&
            $t->getNext()->checkClass('literals')  &&
            $t->getNext(1)->checkCode(';')
            ) {

            $this->args = array(0, 1);
            $this->remove = array( 1);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 

        if ($t->checkToken(T_BREAK) &&
            $t->getNext()->checkClass('parentheses')  &&
            $t->getNext(1)->checkCode(';')
            ) {

            $this->args = array(0, 1);
            $this->remove = array( 1);

            mon_log(get_class($t)." =>2 ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>