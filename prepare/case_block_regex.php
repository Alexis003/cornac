<?php

class case_block_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_CASE);
    }
    
    function check($t) {
        if (!$t->hasNext()) { return false; }
        
        if ($t->getNext()->checkClass('Token')) { return false; }
        if ($t->getNext(1)->checkNotCode(array(':',';'))) { return false; }
        if ($t->getNext(2)->checkNotClass('block')) { return false; }

        if ($t->getNext(3)->checkNotClass(array('_default','_case')) &&
            $t->getNext(3)->checkNotCode('}') &&
            $t->getNext(3)->checkNotToken(array(T_CASE, T_DEFAULT, T_ENDSWITCH))) { return false;}

        $this->args = array(0, 1, 3 );
        $this->remove = array(1,2,3);

        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>