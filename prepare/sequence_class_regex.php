<?php

class sequence_class_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(0);
    } 

    function check($t) {
        if (!$t->hasNext() ) { return false; }

        if (!$t->checkForBlock(true) && $t->checkNotClass(array('codephp','rawtext'))) { return false; } 
        if (!$t->getNext()->checkForBlock(true) && 
            !$t->getNext()->checkForVariable() &&
            $t->getNext()->checkNotClass(array('parentheses')) ) { return false; } 
        if ( (!$t->hasNext(1) || 
               ($t->getNext(1)->checkNotCode(array('or','and','xor','->','[','::',')','.','||','&&','++','--','+','-','/','*','%')) &&
                !$t->getNext(1)->checkForAssignation()) &&
                $t->getNext(1)->checkNotClass('arglist'))
               ) { 

            if ($t->hasNext(1) && $t->getNext(1)->checkCode(array('=','->',',','('))) { return false; }
            if ($t->hasPrev() && ($t->getPrev()->checkCode(array(')','->','.','?','"')) ||
                                  $t->getPrev()->checkClass(array('parentheses','arglist')) ||
                                  $t->getPrev()->checkToken(array(T_ELSE, T_ABSTRACT))) ) { return false; }

            $var = $t->getNext(1); 
            $this->args   = array( 0, 1 );
            $this->remove = array( 1 );
                        
            mon_log(get_class($t)." repère une sequence ( ".get_class($t).", ".get_class($t->getNext())." )  (".__CLASS__.")");
            return true; 
        } 
        
        return false;
    }

}
?>