<?php

class clone_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_CLONE);
    }
 
    
    function check($t) {
        if (!$t->hasNext()) { return false; }

        if ($t->checkToken(T_CLONE) &&
            $t->getNext()->checkClass(array('variable','tableau','property','property_static','method','method_static','functioncall')) &&
//            $t->getNext(1)->checkCode(array(';'))
                $t->getNext(1)->checkEndInstruction()
            ) {

            $this->args = array(1);
            $this->remove = array(1);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>