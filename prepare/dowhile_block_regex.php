<?php

class dowhile_block_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_DO);
    }
    
    function check($t) {
        if (!$t->hasNext(2)) { return false; }

        if ( $t->checkToken(T_DO) &&
             $t->getNext()->checkClass('block') && 
             $t->getNext(1)->checkToken(T_WHILE) &&
             $t->getNext(2)->checkClass('parentheses')
           ) {

            $this->args = array(3, 1 );
            $this->remove = array(1, 2, 3 );

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>