<?php

class variable_accoladeseparee_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('$');
    }
    
    function check($t) {
        if (!$t->hasNext(3) ) { return false; }

        if ($t->checkNotCode('$')) { return false;}
        if ($t->getNext()->checkNotCode('{')) { return false;}
        if ($t->getNext(1)->checkClass('Token')) { return false;}
        if ($t->getNext(2)->checkNotCode('}')) { return false;}
        
        if ($t->getNext()->checkCode('{') &&
            $t->getNext(1)->checkNotClass('Token') &&
            $t->getNext(2)->checkCode('}')
            ) {
            $this->args   = array(2);
            $this->remove = array(1,2,3);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>