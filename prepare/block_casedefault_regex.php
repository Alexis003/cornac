<?php

class block_casedefault_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(0);
    }
    
    function check($t) {
        if ($t->checkNotCode('{') )   { return false; }
        if ($t->checkClass('block') ) { return false; }
        if (!$t->hasNext())           { return false; }

        $this->remove[] = 0;
        
        $var = $t->getNext();            
        $i = 1;

        while($var->checkNotCode('}')) {
            if ($var->checkClass(array('_case','_default'))) {
                $this->args[] = $i;
                $this->remove[] = $i;
                
                if (!$var->hasNext()) { 
                    return $t; 
                }
                $var = $var->getNext();
                $i++;
                continue;
            }

            if ($var->checkCode('{') ) {
                // @doc nested blocks? aborting.
                $this->args = array();
                $this->remove = array();
                return false;
            }

            // @doc Can't be processed? Just abort
            $this->args = array();
            $this->remove = array();
            return false;
        }

        
        $this->remove[] = $i ; // } final

        mon_log(get_class($t)." => ".__CLASS__);
        return true;
    }
}
?>