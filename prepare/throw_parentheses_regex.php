<?php

class throw_parentheses_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_THROW );
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->checkToken(T_THROW) &&
            $t->getNext()->checkOperateur('(') &&
            $t->getNext(1)->checkClass(array('_new','variable','property','method','tableau','method_static','functioncall')) &&
            $t->getNext(2)->checkOperateur(')') &&
            $t->getNext(3)->checkNotCode(array('->','['))
            ) {

            $this->args = array(2);
            $this->remove = array( 1,2,3);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>