<?php

class concatenation_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(".");
    }
 
    function check($t) {
        if (!$t->hasNext() ) { return false; }
        if (!$t->hasPrev( 1 )) { return false; }

        if ($t->getPrev()->checkClass(array('Token','arglist'))) { return false; }
        if ($t->getPrev(1)->checkOperateur(array('.','->','@','::','++','--'))) { return false; }
        
        $var = $t; 
        $this->args   = array( -1 );
        $this->remove = array( -1 );
        
        $pos = 0;
        
        while ($var->checkCode('.') && 
               $var->getNext()->checkNotClass(array('Token','arglist'))) {

            $this->args[]    = $pos + 1;

            $this->remove[]  = $pos;
            $this->remove[]  = $pos + 1;
            
            $pos += 2;
            $var = $var->getNext(1);
        }
        
        if ($var->checkEndInstruction()) {
            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } else {
            $this->args = array();
            $this->remove = array();
            return false;
        }
    }
}
?>