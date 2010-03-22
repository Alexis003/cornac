<?php

class ifthenelse_alternative_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_ELSE);
    }
    
    function check($t) {
        if (!$t->hasNext(2) ) { return false; }

        if ($t->checkNotToken(array(T_ELSE))) { return false;} 
        if ($t->getNext()->checkNotCode(':')) { return false; } 
        
        $args = array();
        $remove = array(-1 );
        $var = $t->getNext(1);            
        $pos = 0;

        while($var->checkNotToken(array(T_ENDIF))) {
           if ($var->checkToken(T_IF) ) {
              // Un autre if qui démarre? On aime pas les imbrications
              return false;
           }

            if ($var->checkForBlock()) {
                $args[] = $pos;
                $remove[] = $pos;
                if (!$var->hasNext()) { return $t; }
                $var = $var->getNext();
                $pos++;
                continue;
            }

            if ($var->checkNotClass(array('block','Token')) && 
                $var->getNext()->checkCode(';')) {
                $args[] = $pos;

                $remove[] = $pos;
                $remove[] = $pos + 1;
                if (!$var->hasNext(1)) { return $t; }
                $var = $var->getNext(1);
                $pos += 2;
                continue;
            }

            if ($var->checkCode(';') ) {
                // un point-virgule qui traine. Bah....
                $remove[] = $pos;
                $pos++;
                $var = $var->getNext();
                continue;
            }

            // pas traitable ? On annule tout.
            return false;
        }

        if ($var->checkToken(T_ENDIF)) {
            $remove[] = $pos;
        }
        
        $regex = new modele_regex('block',$args, $remove);
        Token::applyRegex($t->getNext(1), 'block', $regex);

        mon_log(get_class($t)." => block (".__CLASS__.")");
        return false; 
    }
}
?>