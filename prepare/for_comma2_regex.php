<?php

class for_comma2_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_FOR);
    }
    
    function check($t) {
        if (!$t->hasNext(4)) { return false; }

//        if ($t->checkNotToken(array(T_FOR))) { return false; } 
        if ($t->getNext()->checkNotCode(array('('))) { return false; } 
        
        $pos = 0;
        if ($t->getNext(1)->checkOperateur(array(';'))) { 
            $pos++;
        } elseif ($t->getNext(2)->checkOperateur(array(';'))) { 
            $pos += 2;
        } else {
            return false;
        }
        if ($t->getNext($pos + 1)->checkClass(array('Token'))) { return false; } 
        if ($t->getNext($pos + 2)->checkNotCode(array(','))) { return false; } 
        
        $args = array();
        $remove = array();
        $var = $t->getNext($pos + 1);
        $init = $pos + 1;
        $pos = 0;
        
        while($var->checkNotClass('Token') &&
              $var->getNext()->checkCode(',')) {
            
            $args[] = $pos ;
            
            $remove[] = $pos ;
            $remove[] = $pos + 1;
            
            $var = $var->getNext(1);
            $pos += 2;
        }

        if ($var->checkNotClass('Token') &&
           $var->getNext()->checkCode(';')) {
            $args[] = $pos ;
            
            $remove[] = $pos ;
            
            $regex = new modele_regex('block',$args, $remove);
            Token::applyRegex($t->getNext($init), 'block', $regex);

            mon_log(get_class($t)." => block (position 2) (from ".get_class($t->getNext(3)).") (".__CLASS__.")");
            return false; 
        } 
        
        return false;
    }
}

?>