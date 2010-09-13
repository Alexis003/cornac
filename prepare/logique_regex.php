<?php

class logique_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_LOGICAL_OR, T_LOGICAL_AND, T_LOGICAL_XOR, T_BOOLEAN_OR, T_BOOLEAN_AND, '&','|','^' );
    }
    
    function check($t) {
        if (!$t->hasPrev() ) { return false; }
        if (!$t->hasNext() ) { return false; }

        if ($t->checkNotToken(array(T_LOGICAL_OR, T_LOGICAL_AND, T_LOGICAL_XOR, 
                                    T_BOOLEAN_OR, T_BOOLEAN_AND )) &&
            $t->checkNotCode(array('&','|','^'))) { return false;}
            
        if ($t->checkClass('literals')) { return false; }
        if ($t->getPrev()->checkClass(array( 'arglist','sequence','block'))) { return false;}
        if ($t->getPrev()->checkCode(array( ')',','))) { return false;}
        if ($t->getPrev()->checkForAssignation()) { return false;}

        if ($t->getNext()->checkClass(array('Token', 'arglist','sequence','block'))) { return false;}

        if ((!$t->hasPrev(2) || ($t->getPrev(1)->checkBeginInstruction()) || 
                                 $t->getPrev(1)->checkCode(')') ) &&
            (!$t->hasNext(2) || ($t->getNext(1)->checkNotCode(array('[','->','{','(','::')) && !$t->getNext(1)->checkForAssignation())) && 
            (!$t->hasNext(2) || $t->getNext(1)->checkNotClass(array('parentheses')))

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