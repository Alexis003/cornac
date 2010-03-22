<?php

class variable_accolade_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('${');
    }
    
    function check($t) {
        if (!$t->hasNext(1) ) { return false; }

        if ($t->checkNotCode('${')) { return false;}
        if ($t->getNext()->checkNotClass(array('variable','tableau'))) { return false;}
        if ($t->getNext(1)->checkNotCode('}')) { return false;}
        
        $this->args   = array(1);
        $this->remove = array(1,2);

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>