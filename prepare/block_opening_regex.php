<?php

class block_opening_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_OPEN_TAG);
    }
    
    function check($t) {
        if (!$t->hasNext(2))           { return false; }
        if ($t->getNext()->checkClass('Token') )   { return false; }
        if ($t->getNext(1)->checkNotOperateur(';') )   { return false; }
        if (!$t->getNext(2)->checkForBlock() &&
            !$t->getNext(2)->checkForVariable())   { return false; }

        $regex = new modele_regex('block',array(0), array(0, 1));
        Token::applyRegex($t->getNext(), 'block', $regex);
        
        mon_log(get_class($t)." (".__CLASS__.")  => Block");
        return false;
    }
}
?>