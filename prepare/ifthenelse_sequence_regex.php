<?php

class ifthenelse_sequence_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_ELSE);
    }
    
    function check($t) {
        if (!$t->hasNext(1) ) { return false; }

        if ($t->getNext()->checkNotClass('sequence')) { return false; }

        $regex = new modele_regex('block',array(0), array());
        Token::applyRegex($t->getNext(), 'block', $regex);

        mon_log(get_class($t)." => block (".__CLASS__.")");
        return false; 
    }
}
?>