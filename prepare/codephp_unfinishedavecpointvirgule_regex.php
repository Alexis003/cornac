<?php

class codephp_unfinishedavecpointvirgule_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_OPEN_TAG);
    }
    
    function check($t) {
        if (!$t->hasNext(1)) { return false; }

        if ($t->getNext()->checkNotClass('Token') && 
            $t->getNext(1)->checkCode(';') && 
            is_null($t->getNext(2))) {
            $this->args = array(1);
            $this->remove = array(1,2);
            
            mon_log(get_class($t)." => codePHP (".__CLASS__.")");
            return true;
        } 
        return false;
    }
}

?>