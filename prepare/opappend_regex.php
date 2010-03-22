<?php

class opappend_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('[');
    }
    
    function check($t) {
//        if (!$t->hasNext(1)) { return false; }
        if (!$t->hasPrev(1)) { return false; }
        
        if ($t->hasPrev(1) && $t->getPrev(1)->checkCode(array('::','->'))) { return false; }

        if ($t->getNext()->checkNotCode(']')) { return false; }
        if ($t->getPrev()->checkNotClass(array('variable','property','tableau','property_static','opappend'))) { return false; }

        $this->args = array(-1);
        $this->remove = array(-1, 0, 1);

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>