<?php

class new_variable_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_NEW);
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->checkNotToken(T_NEW)) { return false; }
        if ($t->getNext()->checkNotClass(array('variable','tableau','method','property','property_static','method_static'))) { return false; }

        if (!$t->getNext(1)->checkEndInstruction()) { return false; }

        $this->args = array(1);
        $this->remove = array(1);
        
        if ( $t->hasNext(3) &&
             $t->getNext(1)->checkCode('(') &&
             $t->getNext(2)->checkCode(')')
             ) {

            $this->args[]   = 2;
            $this->args[]   = 3;
            $this->remove[] = 2;
            $this->remove[] = 3;
       } 

        if ( $t->getNext(1)->checkClass('parentheses')) {
            $this->args[]   = 2;
            $this->remove[] = 2;
        } 

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>