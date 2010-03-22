<?php

class dowhile_apres_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_WHILE);
    }
    
    function check($t) {
        if (!$t->hasNext(2)) { return false; }
        if (!$t->hasPrev()) { return false; }

        if ($t->checkToken(T_WHILE) &&
            $t->getNext()->checkClass('parentheses') &&
            $t->getNext(1)->checkCode(';') &&
            $t->getPrev()->checkClass('block')
            ) {
            
            if ($t->hasPrev(1) && $t->getPrev(1)->checkToken(T_DO)) { return false; }

            $this->args = array( 1, -1 );
            $this->remove = array(-1, 1);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>