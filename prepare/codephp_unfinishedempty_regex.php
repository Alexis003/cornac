<?php

class codephp_unfinishedempty_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_OPEN_TAG);
    }
    
    function check($t) {
        if (!$t->hasNext()) {
            $this->args = array();
            $this->remove = array();
            
            mon_log(get_class($t)." => ".__CLASS__);
            return true;
        } 
        return false;
    }
}

?>