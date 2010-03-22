<?php

class variable_variable_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array('$');
    }
    
    function check($t) {
    
        if (!$t->hasPrev() ) { return false; }

        if ($t->checkCode('$') &&
            $t->getNext()->checkClass('variable')
            ) {

            $this->args   = array(0, 1);
            $this->remove = array(1);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>