<?php

class functioncall_shorttag_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_OPEN_TAG);
    }

    function check($t) {
        if (!$t->hasNext(2) ) { return false; }
        
        if ($t->getNext()->checkNotCode(array("="))) { return false; }
        if ($t->getNext()->checkNotClass(array("Token"))) { return false; }
        if ($t->getNext(1)->checkClass(array("Token"))) { return false; }
        if ($t->getNext(2)->checkCode(array("[","::",'->','('))) { return false; }
        
        $args = array(0, 1);
        $delete = array( 1);
        
        if ($t->getNext(2)->checkCode(';')) {
            // args : non
            $delete[] = 2;
        }
        
        $regex = new modele_regex('functioncall',$args,$delete);
        Token::applyRegex($t->getNext(), 'functioncall', $regex);

        mon_log(get_class($t)." => echo block (from <?= ) (".__CLASS__.")");
    }
}
?>