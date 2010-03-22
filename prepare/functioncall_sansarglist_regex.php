<?php

class functioncall_sansarglist_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_EXIT);
    }

    function check($t) {
        if (!$t->hasNext() ) { return false; }
        
        if ($t->checkToken(array(T_EXIT)) &&
            $t->getNext()->checkCode(';')
            ) {
                $this->args = array(0 );
                $this->remove = array();

                mon_log(get_class($t)." => ".__CLASS__);
                return true; 
            }
        
        return false;
    }
}
?>