<?php

class preplusplus_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_DEC,T_INC);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }

        if ($t->checkToken(array(T_DEC,T_INC)) &&
            $t->getNext()->checkClass(array('variable','tableau','property','property_static')) && 
            $t->getNext(1)->checkNotCode(array('[','->'))
            ) {

            $this->args = array(0, 1);
            $this->remove = array(1);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>