<?php

class try_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_TRY );
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->checkNotToken(T_TRY)) { return false; } 
        if ($t->getNext()->checkNotClass('block')) { return false; } 
        if ($t->getNext(1)->checkNotClass('_catch')) { return false; } 

        $this->args = array(1, 2);
        $this->remove = array(1,2);
        $var = $t->getNext(2);
        $pos = 3;
        
        if (is_null($var)) {
            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        }
        
        while($var->checkClass('_catch')) {
            $this->args[] = $pos;
            $this->remove[] = $pos;

            $pos ++;
            $var = $var->getNext();
            if (is_null($var)) {
                mon_log(get_class($t)." => ".__CLASS__);
                return true; 
            }
        }
                
        if ($var->checkToken(T_CATCH)) {
            $this->args = array();
            $this->remove = array();
            return false;
        }

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>