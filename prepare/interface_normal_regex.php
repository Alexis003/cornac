<?php

class interface_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_INTERFACE );
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }
        if ($t->checkNotToken(T_INTERFACE)) { return false; } 

        $this->args = array(1);
        $this->remove = array(1);
        
        $pos = 1;
        $var = $t->getNext(1);
        if ($var->checkToken(T_EXTENDS)) {
            $this->args[] = $pos + 2;
            $this->remove[] = $pos + 1;
            $this->remove[] = $pos + 2;

            $var = $var->getNext(1);
            $pos = $pos + 2;

            while ($var->checkCode(',')) {
                $this->args[] = $pos + 2;
                $this->remove[] = $pos + 1;
                $this->remove[] = $pos + 2;

                $var = $var->getNext(1);
                $pos = $pos + 2;
            }
        }
        
        if ($var->checkNotClass('block')) { return false; } 

        $this->args[] = $pos + 1;
        $this->remove[] = $pos + 1;
        
        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>