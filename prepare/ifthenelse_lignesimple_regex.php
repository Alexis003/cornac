<?php

class ifthenelse_lignesimple_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }
    
    function check($t) {
        if (!$t->hasNext(6) ) { return false; }

        if ($t->checkToken(T_IF) &&
            $t->getNext()->checkClass('parentheses') &&
            $t->getNext(1)->checkNotClass('Token') &&
            $t->getNext(2)->checkCode(';') &&
            $t->getNext(3)->checkToken(T_ELSE) &&
            $t->getNext(4)->checkNotClass('Token') &&
            $t->getNext(5)->checkCode(';') &&
            ) {

            $this->args   = array(1, 2, 5);
            $this->remove = array(1, 2, 3, 4, 5, 6);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>