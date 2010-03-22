<?php

class sequence_empty_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(0);
    }
 
    function check($t) {
        if (!$t->hasNext() ) { return false; }

        if ($t->checkClass('sequence') && 
            $t->getNext()->checkCode(";")) { 

            $var = $t->getNext(1); 
            $this->args   = array( 0 );
            $this->remove = array( 1 );
            
            $pos = 2;
            
            while ($var->checkCode(";") ) {
                $this->remove[]  = $pos;
                $pos += 1;
                $var = $var->getNext();
            } 
            
            mon_log(get_class($t)." supprime ".count($this->args)." point-virgules (".__CLASS__.")");
            return true; 
        } 
        
        $this->args = array();
        $this->remove = array();
        return false;
    }

}
?>