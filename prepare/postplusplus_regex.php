<?php

class postplusplus_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_DEC,T_INC);
    }
    
    function check($t) {
        if (!$t->hasPrev()) { return false; }

        if ($t->hasPrev(1) && $t->getPrev(1)->checkCode(array('::','$'))) { return false; }
        if ($t->getPrev()->checkClass(array('variable','tableau','property','property_static'))) {

            $this->args = array(-1, 0);
            $this->remove = array(-1);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>