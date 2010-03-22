<?php

class default_block_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_DEFAULT);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }

        if ($t->getNext()->checkNotCode(array(':',';'))) { return false; }
        if ($t->getNext(1)->checkNotClass('block') &&
            $t->getNext(1)->checkNotToken(T_ENDSWITCH, T_CASE)) { return false; }

        $this->args = array( 2 );
        $this->remove = array(1,2);

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>