<?php

class signe_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('+','-');
    }
    
    function check($t) {
        if (!$t->hasPrev()) { return false; }
        if (!$t->hasNext()) { return false; }

        if ( $t->getNext()->checkNotClass(array('variable','property','tableau',
                                                'method','functioncall','constante',
                                                'literals','parentheses','operation',
                                                'cast'))) { return false; }
        if ( $t->getPrev()->checkClass(array('literals','variable','tableau',
                                             'property','operation','signe',
                                             'functioncall','parentheses','arglist',
                                             'cdtternaire', )) ) { return false ;}

        if (!$t->getPrev()->checkBeginInstruction() &&
             $t->getPrev()->checkNotCode(array('~','@','!'))) { return false; }
             
        if ( $t->getPrev()->checkClass(array('variable','operation','property','property_static'))) { return false; }
        
        if ( $t->getNext(1)->checkCode(array('->','[','*','/','%','++','--')) ) { return false; }
        
        $this->args = array(0, 1 );
        $this->remove = array(1);

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>