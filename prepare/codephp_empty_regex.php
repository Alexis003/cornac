<?php

class codephp_empty_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_OPEN_TAG);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }

        if ($t->checkToken(T_OPEN_TAG) &&
            $t->getNext()->checkToken(T_CLOSE_TAG)) {
            $this->args = array();
            $this->remove = array(1);
            
            mon_log(get_class($t)." => ".__CLASS__);
            return true;
        } 
        return false;
    }
}

?>