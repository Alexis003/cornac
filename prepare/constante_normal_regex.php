<?php

class constante_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_STRING,0);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }
        if (!$t->hasPrev()) { return false; }

        if ($t->checkClass('Token') && ($t->checkToken(T_STRING)) &&
            $t->getNext()->checkNotCode(array('(','::','{'/*,'&'*/)) &&
            $t->getNext()->checkNotToken(T_VARIABLE) && // T_STRING ? 
            $t->getNext()->checkNotClass(array('variable','affectation')) && 
            $t->getPrev()->checkNotCode(array('->')) &&
            $t->getPrev()->checkNotToken(array(T_CLASS))
            )  {

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>