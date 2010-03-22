<?php

class dowhile_simples_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_DO);
    }
    
    function check($t) {
        if (!$t->hasNext(2)) { return false; }

        if (  $t->getNext()->checkClass('block'))   { return false; }
        if ( !$t->getNext()->checkForBlock()) { return false; }
        if (  $t->getNext(1)->checkNotOperateur(';')) { return false; }

        $args = array(0);
        $remove = array(1);
        
        $regex = new modele_regex('block',$args, $remove);
        Token::applyRegex($t->getNext(), 'block', $regex);

        mon_log(get_class($t)." => block (".__CLASS__.")");
        return false; 
    }
}
?>