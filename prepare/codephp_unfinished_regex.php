<?php

class codephp_unfinished_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_OPEN_TAG);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; } 
        if ($t->hasNext(1)) { return false; } 

        if ($t->checkNotToken(T_OPEN_TAG))      { return false; }

        if ($t->getNext()->checkClass('Token')) { return false; }

        $this->args = array(1);
        $this->remove = array(1);
            
        mon_log(get_class($t)." => ".__CLASS__);
        return true;
    }
}

?>