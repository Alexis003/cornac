<?php

class codephp_normal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_OPEN_TAG);
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->checkNotToken(T_OPEN_TAG)) { return false; }

        if ($t->checkToken(T_OPEN_TAG) &&
            $t->getNext()->checkNotClass('Token') && 
            $t->getNext(1)->checkToken(T_CLOSE_TAG)) {
            $this->args = array(1);
            $this->remove = array(1,2);
            
            mon_log(get_class($t)." => ".__CLASS__);
            return true;
        } 
        return false;
    }
}

?>