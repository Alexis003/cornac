<?php

class tableau_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(Token::ANY_TOKEN);
    }
    
    function check($t) {
        if (!$t->hasNext(3) ) { return false; }

        if ($t->checkClass(array('variable','tableau','property','opappend')) &&
            $t->getNext()->checkCode('[') &&
            $t->getNext(1)->checkNotClass('Token') &&
            $t->getNext(2)->checkCode(']')
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