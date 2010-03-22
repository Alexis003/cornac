<?php

class ifthen_blockelseblock_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }
    
    function getTokens() {
        return array(T_IF);
    }

    function check($t) {
        if (!$t->hasNext(3) ) { return false; }

        if ($t->checkToken(T_IF) &&
            $t->getNext()->checkClass('parentheses') &&
            $t->getNext(1)->checkClass(array('block', 'ifthen')) &&
            $t->getNext(2)->checkToken(T_ELSE) &&
            $t->getNext(3)->checkClass(array('block', 'ifthen'))
            ) {

            $this->args   = array(1, 2, 4);
            $this->remove = array(1, 2, 3, 4);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>