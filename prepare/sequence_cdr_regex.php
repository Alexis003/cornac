<?php

class sequence_cdr_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_OPEN_TAG);
    } 

    function check($t) {
        if (!$t->hasNext() ) { return false; }
        if (!$t->hasPrev() ) { return false; }
        
        if ($t->getPrev()->checkNotClass(array('sequence'))) { return false; }
        if ($t->getNext()->checkNotClass(array('sequence'))) { return false; }

        $regex = new modele_regex('sequence',array(-2, 0), array(-2));
        Token::applyRegex($t->getNext(), 'sequence', $regex);

        mon_log(get_class($t)." => spot a rawtext (".__CLASS__.")");
        return false; 
    }
}
?>