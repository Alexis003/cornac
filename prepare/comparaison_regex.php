<?php

class comparaison_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_IS_EQUAL, T_IS_SMALLER_OR_EQUAL, T_IS_NOT_IDENTICAL, 
                     T_IS_NOT_EQUAL, T_IS_IDENTICAL, T_IS_GREATER_OR_EQUAL, 
                     T_INSTANCEOF, 0);
    }
    
    function check($t) {
        if (!$t->hasPrev() ) { return false; }
        if (!$t->hasNext() ) { return false; }
        
        if ($t->hasPrev(2) && ($t->getPrev(1)->checkCode(array('->','$','::','++','--','new','-','+')) ||
                               $t->getPrev(1)->checkClass(array('variable')) ||
                               $t->getPrev(1)->checkForComparison() )) { return false; }

        if ($t->getPrev()->checkNotClass(array('Token','arglist')) &&
            ($t->checkToken(array(T_IS_EQUAL, T_IS_SMALLER_OR_EQUAL, T_IS_NOT_IDENTICAL, T_IS_NOT_EQUAL, T_IS_IDENTICAL, T_IS_GREATER_OR_EQUAL, T_INSTANCEOF)) || 
             $t->checkCode(array('>', '<'))) && 
            $t->getNext()->checkNotClass('Token') && 
            $t->getNext(1)->checkNotCode(array('[','->','+','-','/','*','%','{','++','--','='))
            ) {

            $this->args   = array(-1, 0, 1);
            $this->remove = array(-1, 1);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>