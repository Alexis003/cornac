<?php

class sequence_suite_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(0);
    }
 
    function check($t) {
        if (!$t->hasNext() ) { return false; }

        if ($t->checkNotClass('sequence')) { return false; }
        if ($t->getNext()->checkForBlock(true) || $t->getNext()->checkClass(array('parentheses','codephp'))) { 

            $var = $t->getNext(1); 
            $this->args   = array( 0, 1 );
            $this->remove = array( 1 );
            
            $pos = 2;
            
            if (is_null($var)) {
                mon_log(get_class($t)." fusionne ".count($this->args)." sequences (avant, 1,  ".__CLASS__.")");
                return true; 
            }
            
            while ($var->checkForBlock(true) || $var->checkClass(array('codephp')) ) {
                $this->args[]    = $pos ;
                
                $this->remove[]  = $pos;
                $pos += 1;
                $var = $var->getNext();
                if (is_null($var)) {
                    mon_log(get_class($t)." fusionne ".count($this->args)." sequences (avant, 2, ".__CLASS__.")");
                    return true; 
                }
            } 
            
            if ($var->checkForAssignation() ||
                $var->checkCode(array('or','and','xor','->','[','::',')','.','||','&&'))) {
                $this->args = array();
                $this->remove = array();
                return false;
            }
            
            mon_log(get_class($t)." fusionne ".count($this->args)." sequences (avant, 3, ".__CLASS__.")");
            return true; 
        } 
        
        $this->args = array();
        $this->remove = array();
        return false;
    }

}
?>