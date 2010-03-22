<?php

class ifthenelse_vides_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_IF,T_ELSEIF, T_ELSE);
    }
    
    function check($t) {
        if (!$t->hasNext(1) ) { return false; }

        if ($t->checkToken(array(T_ELSEIF, T_IF)) &&
            $t->getNext()->checkClass('parentheses') &&
            $t->getNext(1)->checkCode(';') 
            ) {
            $regex = new modele_regex('block',array(), array());
            Token::applyRegex($t->getNext(1), 'block', $regex);

            mon_log(get_class($t)." => block 1 (".__CLASS__.")");
            return false; 
        } 

        if ($t->checkToken(array(T_ELSE)) &&
            $t->getNext()->checkCode(';') 
            ) {

            $regex = new modele_regex('block',array(), array());
            Token::applyRegex($t->getNext(), 'block', $regex);

            mon_log(get_class($t)." => block 2 (".__CLASS__.")");
            return false; 
        } 
        return false;
    }
}
?>