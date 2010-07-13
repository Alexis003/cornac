<?php

class clone_parentheses_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_CLONE);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }
        if ($t->getNext()->checkNotCode('(')) { return false; }
        if ($t->getNext(1)->checkNotClass(array('variable','tableau',
                                                'property','property_static',
                                                'method','method_static',
                                               'functioncall'))) { return false; }
        if ($t->getNext(2)->checkNotCode(array(')'))) { return false; }

        $this->args = array(2);
        $this->remove = array(1,2,3);

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>