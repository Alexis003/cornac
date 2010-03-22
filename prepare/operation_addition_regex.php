<?php

class operation_addition_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('+','-');
    }    
    function check($t) {
        if (!$t->hasPrev() ) { return false; }
        if (!$t->hasNext(1)) { return false; }

        if ($t->checkNotCode(array('+','-'))) { return false;}
        if ($t->checkNotClass("Token")) { return false;} 
        if ($t->getPrev()->checkClass(array('Token','arglist'))) { return false;}
        if ($t->getNext()->checkClass('Token')) { return false;}
        
        if (
            $t->getNext(1)->checkNotCode(array('*','/','%','[','->','{','(','++','--')) &&
            !$t->getNext(1)->checkForAssignation() &&
            $t->getPrev(1)->checkBeginInstruction()
            
            ) {

            $this->args = array(-1, 0, 1);
            $this->remove = array(-1, 1);

            mon_log(get_class($t)." => operation addition  (".__CLASS__.")");
            return true; 
        } 
        return false;
    }
}
?>