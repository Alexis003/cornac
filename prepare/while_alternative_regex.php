<?php

class while_alternative_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_WHILE);
    }

    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->getNext()->checkNotClass('parentheses')) { return false; }
        if ($t->getNext(1)->checkNotOperateur(':')) { return false; }

        $var = $t->getNext(2);
        $init = $var;
        
        $args = array();
        $remove = array(-1);
        $pos = 0;
 
         while($var->checkNotToken(T_ENDWHILE)) {
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

            if ($var->checkToken(T_WHILE) ) {
                // while imbriqués ? Alors, on annule tout.
                $args = array();
                $remove = array();
                return false;
            }

            if ($var->checkOperateur(';') ) {
                // un point-virgule qui traine. Bah....
                $remove[] = $pos;
                $pos++;
                $var = $var->getNext();
                continue;
            }

            // pas traitable ? On annule tout.
            return false;
        }
        
        $remove[] = $pos;

        $regex = new modele_regex('block',$args, $remove);
        Token::applyRegex($init, 'block', $regex);

        mon_log(get_class($t)." => block (".__CLASS__.")");
        return false; 
    }
}
?>