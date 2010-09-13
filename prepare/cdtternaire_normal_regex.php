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

        if ($t->hasPrev(1) && $t->getPrev(1)->checkCode(array('::','->','@'))) { return false; }

// case of the ?   :
        if ($t->getPrev()->checkNotClass(array('Token','arglist')) &&
            $t->checkCode('?') &&
            $t->getNext()->checkNotClass('Token') &&
            $t->getNext(1)->checkCode(':') &&
            $t->getNext(2)->checkNotClass('Token') &&
            $t->getNext(3)->checkNotCode(array('->','[','(','::')) &&
           !$t->getNext(3)->checkForAssignation()
            ) {
                $this->args = array(-1, 1, 3);
                $this->remove = array( -1, 1, 2, 3);
    
                mon_log(get_class($t)." => ? : ".__CLASS__);
                return true;
            } 

// case of the ?:
        if ($t->getPrev()->checkNotClass(array('Token','arglist')) &&
            $t->checkCode('?') &&
//            $t->getNext()->checkNotClass('Token') &&
            $t->getNext()->checkCode(':') &&
            $t->getNext(1)->checkNotClass('Token') &&
            $t->getNext(2)->checkNotCode(array('->','[','(','::')) &&
           !$t->getNext(2)->checkForAssignation()
            ) {
                $regex = new modele_regex('block',array(), array());
                Token::applyRegex($t->getNext(), 'block', $regex);

                $this->args = array(-1, 1, 2);
                $this->remove = array( -1, 1, 2);
    
                mon_log(get_class($t)." => ?: ".__CLASS__);
                return true;
            } 
            
            return false;
    }
}

?>