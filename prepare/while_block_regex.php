<?php

class while_block_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_WHILE);
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->checkToken(T_WHILE) &&
            $t->getNext()->checkClass('parentheses') &&
            $t->getNext(1)->checkClass('block')
            ) {

            $this->args = array(1, 2 );
            $this->remove = array(1, 2);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>