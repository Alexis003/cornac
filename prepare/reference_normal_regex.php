<?php

class reference_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('&');
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }

        if ( $t->checkClass('literals')) { return false; }
        if ( $t->getNext(1)->checkCode(array('->','[','(','::'))) { return false; }

        if ($t->getPrev()->checkToken(T_AS)) {
            // continue, c'est une exception
        } elseif ($t->getPrev()->checkClass(array('arglist','functioncall','parentheses'))) {
            return false;
        } elseif (!$t->getPrev()->checkBeginInstruction()) {  
            return false; 
        }
        
        if ($t->getNext()->checkClass(array('variable','_new','method','functioncall','_new','property','tableau','property_static','method_static','opappend'))) {

            $this->args = array(1);
            $this->remove = array(1);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>