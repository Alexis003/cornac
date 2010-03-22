<?php

class signe_suite_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('+','-');
    }
    
    function check($t) {
        if (!$t->hasPrev()) { return false; }
        if (!$t->hasNext()) { return false; }

        if ( $t->getPrev()->checkNotCode(array('+','-'))) { return false; }
        if ( $t->getPrev()->checkClass(array('operation'))) { return false; }
        if ( $t->getNext()->checkNotClass(array('signe','variable','property','property_static','method','method_static','functioncall','constante','literal')) ) { return false ;}
        if ( $t->getNext(1)->checkCode(array('->','[','{','::','++','--'))) { return false; }
        
        $this->args = array(0, 1 );
        $this->remove = array(1);

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>