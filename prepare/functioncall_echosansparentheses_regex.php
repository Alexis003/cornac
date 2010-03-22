<?php

class functioncall_echosansparentheses_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_ECHO);
    }

    function check($t) {
        if (!$t->hasNext(1) ) { return false; }
        
        if ($t->checkToken(array(T_ECHO)) && 
            $t->getNext()->checkNotCode('('))             
        {
            $var = $t->getNext(); 
            $args   = array();
            $remove = array();
        
            $pos = 0;
        
            while ($var->checkNotClass('Token') && $var->checkNotCode(array(';',',')) &&
                   $var->getNext()->checkCode(',')) {

                $args[]    = $pos;

                $remove[]  = $pos;
                $remove[]  = $pos + 1;
            
                $pos += 2;
                $var = $var->getNext(1);
            }

            if ($var->checkNotClass(array('Token','arglist')) && 
                $var->getNext()->checkEndInstruction() &&
                $var->getNext()->checkNotClass('parentheses')
                ) {
                $args[]    = $pos;
                $remove[]  = $pos;

                $regex = new modele_regex('arglist',$args, $remove);
                Token::applyRegex($t->getNext(), 'arglist', $regex);

                mon_log(get_class($t)." => arglist (".__CLASS__.")");
                return false; 
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
?>