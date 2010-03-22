<?php

class functioncall_variableempty_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(0);
    }

    function check($t) {
        if (!$t->hasNext(2) ) { return false; }

        if ($t->checkClass(array('variable','tableau')) &&
            $t->getNext()->checkCode('(') && 
            $t->getNext(1)->checkCode(')')
            ) {
                $this->args = array(0 );
                $this->remove = array( 1, 2);

                mon_log(get_class($t)." => ".__CLASS__);
                return true; 
            }
        
        return false;
    }
}
?>