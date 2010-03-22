<?php

class ifthenelse_alternativeblock_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_IF);
    }
    
    function check($t) {
        if (!$t->hasNext(1) ) { return false; }

        if (!$t->checkToken(array(T_IF))) { return false;} 
        if ($t->getNext()->checkNotClass('parentheses')) { return false; }
        if ($t->getNext(1)->checkNotCode(':')) { return false; } 
        if ($t->getNext(2)->checkNotClass('block')) { return false; } 
        
        $this->args   = array(1, 3);
        $this->remove = array(1, 2, 3);
        $var = $t->getNext(3);
        $pos = 4;

        while ($var->checkToken(T_ELSEIF) &&
               $var->getNext()->checkClass('parentheses') &&
               $var->getNext(1)->checkCode(':') &&
               $var->getNext(2)->checkClass('block')
               ) {

            $this->args[] = $pos + 1;
            $this->args[] = $pos + 3;
            
            $this->remove[] = $pos;
            $this->remove[] = $pos + 1;
            $this->remove[] = $pos + 2;
            $this->remove[] = $pos + 3;
            
            $var = $var->getNext(3);
            $pos += 4;
        }

        if ($var->checkToken(T_ELSE) &&
            $var->getNext()->checkCode(':') &&
            $var->getNext(1)->checkClass('block')) {
            $this->args[] = $pos + 2;

            $this->remove[] = $pos;
            $this->remove[] = $pos + 1;
            $this->remove[] = $pos + 2;
            
            $var = $var->getNext(2);
            $pos += 3;
        }

        if ($var->checkToken(T_ENDIF)) {
            $this->remove[] = $pos;
            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        }
        
        $this->args = array();
        $this->remove = array();
        return false;

    }
}
?>