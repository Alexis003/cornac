<?php

class continue_leveled_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_CONTINUE);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }

        if ($t->checkToken(T_CONTINUE) &&
            $t->getNext()->checkClass('literals')  &&
            $t->getNext(1)->checkCode(';')
            ) {

            $this->args = array(0, 1);
            $this->remove = array( 1);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 

        if ($t->checkToken(T_CONTINUE) &&
            $t->getNext()->checkCode('(') &&
            $t->getNext(1)->checkClass('literals')  &&
            $t->getNext(2)->checkCode(')') &&
            $t->getNext(3)->checkCode(';')
            ) {

            $this->args = array(0, 2);
            $this->remove = array( 1,2,3);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>