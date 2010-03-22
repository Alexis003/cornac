<?php

class while_noblock_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_WHILE);
    }

    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->getNext()->checkNotClass('parentheses')) { return false; }

        if ($t->getNext(1)->checkCode(';') &&
            $t->getPrev()->checkNotOperateur('}')) {
            $regex = new modele_regex('block',array(), array());
            Token::applyRegex($t->getNext(1), 'block', $regex);

            mon_log(get_class($t)." => block point-virgule (from ".get_class($t->getNext(1)).") (".__CLASS__.")");
            return false; 
        }

        if ($t->getNext(1)->checkClass(array('Token','block'))) { return false;}
        if ($t->getNext(2)->checkCode(array('->','::','[','('))) { return false; }
        if ($t->getNext(2)->checkForAssignation()) { return false; }

        $regex = new modele_regex('block',array(0), array());
        Token::applyRegex($t->getNext(1), 'block', $regex);

        mon_log(get_class($t)." => block (from ".get_class($t->getNext(1)).") (".__CLASS__.")");
        return false; 
    }
}
?>