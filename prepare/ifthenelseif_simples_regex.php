<?php

class ifthenelseif_simples_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_IF,T_ELSEIF);
    }
    
    function check($t) {
        if (!$t->hasNext(1) ) { return false; }
        if ($t->getNext()->checkNotClass('parentheses')) { return false; }

        if ($t->getNext(1)->checkForBlock(true)) {
            if ($t->hasNext(2) && $t->getNext(2)->checkForAssignation()) {
                return false;
            }

            if ($t->hasNext(2) && $t->getNext(2)->checkCode(array('->','['))) {
                return false;
            }
            
            $remove = array();
            if ($t->hasNext(2) && $t->getNext(2)->checkCode(';')) {
                $remove = array(1);
            }
            $regex = new modele_regex('block',array(0),$remove);
            Token::applyRegex($t->getNext(1), 'block', $regex);

            mon_log(get_class($t)." => block 1 (".__CLASS__.")");
            return false; 
        } 

        if ($t->getNext(1)->checkNotClass('Token') &&
            $t->getNext(2)->checkCode(';')) {

            $regex = new modele_regex('block',array(0), array(1));
            Token::applyRegex($t->getNext(1), 'block', $regex);

            mon_log(get_class($t)." => block 2 (".__CLASS__.")");
            return false; 
        } 
        
        return false;
    }
}
?>