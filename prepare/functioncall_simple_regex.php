<?php

class functioncall_simple_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(0);
    }

    function check($t) {
        if (!$t->hasNext() ) { return false; }

        if ($t->hasPrev(2) && 
            $t->getPrev()->checkOperateur('&') &&
            $t->getPrev(1)->checkToken(T_FUNCTION)) { return false; }

        if ((!$t->hasPrev() || 
             $t->getPrev()->checkNotToken(T_FUNCTION)) &&
            $t->checkFunction() &&
            $t->getNext()->checkClass('arglist')) {

            if ($t->getNext(1)->checkNotOperateur(array('{','(')) &&
             $t->getNext(1)->checkNotClass('parentheses')) {
                $this->args = array(0 , 1);
                $this->remove[] = 1;

                mon_log(get_class($t)." => ".__CLASS__);
                return true; 
            } else {
                return false;
            }
        }
        
        return false;
    }
}
?>