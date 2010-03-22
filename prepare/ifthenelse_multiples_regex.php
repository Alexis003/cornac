<?php

class ifthenelse_multiples_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }
    
    function getTokens() {
        return array(T_IF);
    }

    function check($t) {
        if (!$t->hasNext(4) ) { return false; }

        
        if ($t->checkNotToken(T_IF)) { return false;}
        if ($t->getNext()->checkNotClass('parentheses')) { return false;} 

        if ($t->getNext(1)->checkClass('block') &&
            $t->getNext(2)->checkToken(T_ELSEIF) &&
            $t->getNext(3)->checkClass('parentheses') &&
            $t->getNext(4)->checkClass('block')
            ) {

            $this->args   = array(1, 2, 4, 5);
            $this->remove = array(1, 2, 3, 4, 5);

            $var = $t->getNext(5);
            if (is_null($var)) {
               mon_log(get_class($t)." => ".__CLASS__." ".count($this->args).": NULL :");
               return true; 
            }
            $pos = 5;
            while($var->checkToken(T_ELSEIF) &&
                  $var->getNext()->checkClass('parentheses') &&
                  $var->getNext(1)->checkClass('block')) {
                  
                  $this->args[] = $pos + 2;
                  $this->args[] = $pos + 3;

                  $this->remove[] = $pos ;
                  $this->remove[] = $pos + 1;
                  $this->remove[] = $pos + 2;
                  
                  $pos += 3;
                  $var = $var->getNext(2);
            }

            if   ($var->checkToken(T_ELSEIF)) {
                $this->args = array();
                $this->remove = array();
                
                return false;
            }
            
            if   ($var->checkToken(T_ELSE)) {
                if ($var->getNext()->checkClass('block')) {

                  $this->args[] = $pos + 2;

                  $this->remove[] = $pos ;
                  $this->remove[] = $pos + 1;              
                } else {
                    $this->args = array();
                    $this->remove = array();
                    
                    return false;
                }
            }
                        
            mon_log(get_class($t)." => ".__CLASS__." ".count($this->args).": $var :");
            return true; 
        } 
        return false;
    }
}
?>