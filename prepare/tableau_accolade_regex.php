<?php

class tableau_accolade_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(Token::ANY_TOKEN);
    }
    
    function check($t) {
    
        if (!$t->hasPrev() ) { return false; }
        if (!$t->hasNext() ) { return false; }

        if ($t->checkNotClass(array('variable','property','tableau'))) { return false; } 
        if ($t->getNext()->checkCode('{') &&
            $t->getNext(1)->checkNotClass('Token') &&
            $t->getNext(2)->checkCode('}')
            ) {

            $this->args   = array(0, 2);
            $this->remove = array(1,2,3);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>