<?php

class functioncall_variable_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(Token::ANY_TOKEN);
    }
 
    function check($t) {
        if (!$t->hasNext() ) { return false; }

        if ($t->checkClass(array('variable','tableau')) &&
            $t->getNext()->checkClass(array('parentheses','arglist'))
            ) {
                $this->args = array(0 , 1);
                $this->remove[] = 1;

                mon_log(get_class($t)." => ".__CLASS__);
                return true; 
            }
        
        return false;
    }
}
?>