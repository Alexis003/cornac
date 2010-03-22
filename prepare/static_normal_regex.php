<?php

class static_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_STATIC);
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->hasPrev() && $t->getPrev()->checkToken(array(T_PROTECTED, T_PUBLIC, T_PRIVATE))) { return false; }
        
        if ($t->checkToken(T_STATIC) &&
            $t->getNext()->checkClass('variable') &&
            $t->getNext(1)->checkCode(';')
            ) {

            $this->args = array(1);
            $this->remove = array(1,2);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>