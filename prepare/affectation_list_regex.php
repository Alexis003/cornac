<?php

class affectation_list_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }
    
    function getTokens() {
        return array(0);
    }
    
    function check($t) {
        if (!$t->hasPrev()) { return false; }
        if (!$t->hasNext(1)) { return false; }

        if (!$t->checkForAssignation()) { return false;}
        
        if ($t->hasPrev(2) && $t->getPrev(1)->checkOperateur('@')) { return false; }
        
        if (($t->getPrev()->checkClass('functioncall') && $t->getPrev()->getCode() == 'list') &&
            ($t->getNext()->checkSubclass(array('instruction'))  || 
             $t->getNext()->checkClass(array('variable','tableau','property','method','functioncall'))) &&
             $t->getNext(1)->checkCode(array(';',')'))) {
                $this->args = array(-1, 0, 1);
                $this->remove = array( -1, 1);
    
                mon_log(get_class($t)." => ".__CLASS__);
                return true;
            } else {
                return false;
            }
    }
}

?>