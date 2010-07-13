<?php

class constante_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_STRING,Token::ANY_TOKEN);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }
        if (!$t->hasPrev()) { return false; }

        if ($t->checkNotClass('Token')) { return false; } 
        if ($t->checkNotToken(T_STRING)) { return false; }
        if ($t->getNext()->checkCode(array('(','::','{'))) { return false; }
        if ($t->getNext()->checkToken(T_VARIABLE)) { return false; }
        if ($t->getNext()->checkClass(array('variable','affectation'))) { return false; }
        if ($t->getPrev()->checkCode(array('->'))) { return false; }
        if ($t->getPrev()->checkToken(array(T_CLASS))) { return false; }

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>