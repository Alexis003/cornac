<?php

class affectation_avecpointvirgule_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(Token::ANY_TOKEN);
    }
    
    function check($t) {
        if (!$t->hasPrev()) { return false; }
        if (!$t->hasNext(1)) { return false; }

        if ( !$t->checkForAssignation()) { return false; }

        if ((!$t->hasPrev(1) || $t->getPrev(1)->checkNotCode(array('->','$','::','@')) ) &&
            ($t->getPrev()->checkClass('variable') || $t->getPrev()->checkSubclass('variable') ) &&
            $t->getNext()->checkClass(array('variable','literals','property','property_static')) &&
            $t->getNext(1)->checkCode(';')) {
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