<?php

class for_comma1_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_FOR);
    }
    
    function check($t) {
        if (!$t->hasNext(4)) { return false; }

        if ($t->checkNotToken(array(T_FOR))) { return false; } 
        if ($t->getNext()->checkNotCode(array('('))) { return false; } 
        if ($t->getNext(1)->checkClass(array('Token'))) { return false; } 
        if ($t->getNext(2)->checkNotCode(array(','))) { return false; } 
        
        $args = array(0);
        $remove = array(1);
        $pos = 2;
        $var = $t->getNext(3);
        
        while($var->checkNotClass('Token') &&
              $var->getNext()->checkOperateur(',')) {
            
            $args[] = $pos ;
            
            $remove[] = $pos;
            $remove[] = $pos + 1;
            
            $var = $var->getNext(1);
            $pos += 2;
        }

        if ($var->checkNotClass('Token') &&
           $var->getNext()->checkCode(';')) {
            $args[] = $pos;
            $remove[] = $pos;

            $regex = new modele_regex('block',$args, $remove);
            Token::applyRegex($t->getNext(1), 'block', $regex);

            mon_log(get_class($t)." => block (position 1) (from ".get_class($t->getNext(1)).") (".__CLASS__.")");
            return false; 
        } 
        
        return false;
    }
}

?>