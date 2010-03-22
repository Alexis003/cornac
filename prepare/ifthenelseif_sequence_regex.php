<?php

class ifthenelseif_sequence_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_IF,T_ELSEIF);
    }
    
    function check($t) {
        if (!$t->hasNext(2) ) { return false; }

        if ($t->checkToken(array(T_ELSEIF, T_IF)) &&
            $t->getNext()->checkClass('parentheses') &&
            $t->getNext(1)->checkClass('sequence')
            ) {

            $regex = new modele_regex('block',array(0), array());
            Token::applyRegex($t->getNext(1), 'block', $regex);

            mon_log(get_class($t)." => block (".__CLASS__.")");
            return false; 
        } 
        return false;
    }
}
?>