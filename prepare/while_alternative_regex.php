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
                // @doc nested while ? we abort
                $args = array();
                $remove = array();
                return false;
            }

            if ($var->checkOperateur(';') ) {
                // @doc alone semicolon : just eat it up
                $remove[] = $pos;
                $pos++;
                $var = $var->getNext();
                continue;
            }

            // @doc Not processed? Then, we abort
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