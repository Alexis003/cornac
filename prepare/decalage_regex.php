<?php

class decalage_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_SR, T_SL);
    }
    
    function check($t) {
        if (!$t->hasPrev()) { return false; }
        if (!$t->hasNext(1)) { return false; }

        if (($t->hasPrev(1) && $t->getPrev(1)->checkNotCode(array('->','::'))) &&
            $t->getPrev()->checkNotClass(array('Token','arglist'))  &&
//            $t->checkToken(array(T_SR, T_SL))      &&
            $t->getNext()->checkNotClass('Token')  &&
            $t->getNext(1)->checkNotCode(array('[','->','{')) //',',
            ) {

            $this->args = array(-1, 0, 1);
            $this->remove = array(-1, 1);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>