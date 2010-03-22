<?php

class functioncall_sansparentheses_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_PRINT,T_EXIT);
    }

    function check($t) {
        if (!$t->hasNext(1) ) { return false; }
        
        if ($t->checkToken(array( T_PRINT, T_EXIT)) &&  //, T_ECHO
            $t->getNext()->checkNotClass(array('Token','arglist'))   && 
            $t->getNext(1)->checkEndInstruction()
            )
        {
            $regex = new modele_regex('arglist',array(0), array());
            Token::applyRegex($t->getNext(), 'arglist', $regex);

            mon_log(get_class($t)." => arglist (".__CLASS__.")");
            return false; 
        }
        return false;
    }
}
?>