<?php

class catch_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_CATCH );
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->getNext()->checkOperateur('(') &&
            $t->getNext(3)->checkOperateur(')') &&
            $t->getNext(4)->checkClass('block') 
            ) {
            
            $this->args = array(2, 3, 5);
            $this->remove = array(1,2,3,4,5);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 

        return false;
    }
}
?>