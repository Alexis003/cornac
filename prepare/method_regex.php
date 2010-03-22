<?php

class method_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(0);
    }
    
    function check($t) {
        if (!$t->hasNext() ) { return false; }

        if ($t->hasPrev() && $t->getPrev()->checkCode('->')) { return false; }

        if ($t->checkNotClass(array('variable',
                                    'property',
                                    'tableau',
                                    'method',
                                    'functioncall',
                                    'property_static',
                                    'method_static'))) { return false;}
        if ($t->getNext()->checkNotOperateur('->')) { return false; }
        if ($t->getNext(1)->checkNotClass('functioncall')) { return false; }

        $this->args   = array(0, 2);
        $this->remove = array(1, 2);

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>