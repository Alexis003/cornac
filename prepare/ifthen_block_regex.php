<?php

class ifthen_block_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_IF);
    }
    
    function check($t) {
        if (!$t->hasNext(1) ) { return false; }

        if ($t->getNext()->checkNotClass('parentheses')) { return false; }
        if ($t->getNext(1)->checkNotClass('block')) { return false; } 
        
        if ($t->hasNext(2) && $t->getNext(2)->checkToken(array(T_ELSE, T_ELSEIF))) { return false; }

        $this->args   = array(1, 2);
        $this->remove = array(1, 2);

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>