<?php

class property_static_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_DOUBLE_COLON);
    }
    
    function check($t) {
    
        if (!$t->hasPrev() ) { return false; }
        if (!$t->hasNext() ) { return false; }
        if ( $t->checkNotToken(T_DOUBLE_COLON)) { return false; }

        if ($t->getNext(1)->checkCode(array('[')) &&
            $t->getNext(2)->checkNotCode(array(']'))) { return false;}

        if ($t->getNext(1)->checkCode(array('('))) { return false;}

        if ( ($t->getPrev()->checkToken(array(T_STRING,T_STATIC)) || 
              $t->getPrev()->checkClass(array('variable','tableau'))) &&
              $t->getNext()->checkClass(array('variable','tableau'))
            ) {

            $this->args   = array(-1, 1);
            $this->remove = array(-1, 1);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>