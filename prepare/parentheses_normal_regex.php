<?php

class parentheses_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('(');
    }
    
    function check($t) {
        if (!$t->hasPrev() ) { return false; }
        if (!$t->hasNext(1)) { return false; }
    
        if ($t->getPrev()->checkClass('variable')) { return false; }
        if ($t->getPrev()->checkToken(T_CONTINUE)) { return false; }
        if ($t->getPrev()->checkCode('}')) { return false; }
        if ($t->checkNotOperateur('(')) { return false; }
        if ($t->getNext()->checkClass('Token')) { return false; }
        if ( $t->getNext(1)->checkNotOperateur(')')) { return false; }

        if ($t->getPrev()->checkFunction() ) { 
            if ($t->getPrev()->checkCode('echo')) {
                // on peut continuer, c'est possible 
            } else {
                return false; 
            }
        }

        $this->args = array(1);
        $this->remove = array(1, 2);

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>