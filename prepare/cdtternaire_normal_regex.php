<?php

class cdtternaire_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array();
    }
    
    function check($t) {
        if (!$t->hasPrev()) { return false; }
        if (!$t->hasNext(3)) { return false; }

        if (($t->hasPrev(1) && $t->getPrev(1)->checkNotCode(array('::','->','@'))) &&
            $t->getPrev()->checkNotClass(array('Token','arglist')) &&
            $t->checkCode('?') &&
            $t->getNext()->checkNotClass('Token') &&
            $t->getNext(1)->checkCode(':') &&
            $t->getNext(2)->checkNotClass('Token') &&
            $t->getNext(3)->checkNotCode(array('->','[','(','::')) &&
           !$t->getNext(3)->checkForAssignation()
            ) {
                $this->args = array(-1, 1, 3);
                $this->remove = array( -1, 1, 2, 3);
    
                mon_log(get_class($t)." => ".__CLASS__);
                return true;
            } else {
                return false;
            }
    }
}

?>