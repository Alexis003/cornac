<?php

class inclusion_noparenthesis_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }
    
    function getTokens() {
        return array(T_REQUIRE, T_INCLUDE, T_INCLUDE_ONCE, T_REQUIRE_ONCE);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }

        if ($t->checkToken(array(T_REQUIRE, T_INCLUDE, T_INCLUDE_ONCE, T_REQUIRE_ONCE)) &&
            $t->getNext()->checkNotClass('Token') &&
            $t->getNext(1)->checkNotCode(array(".",'->','::','['))) {

            $this->args = array(1);
            $this->remove = array(1);

            mon_log(get_class($t)." => inclusion (".__CLASS__.")");
            return true; 
        } 
        return false;
    }
}
?>