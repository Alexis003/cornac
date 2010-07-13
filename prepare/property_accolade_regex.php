<?php

class property_accolade_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(Token::ANY_TOKEN);
    }
    
    function check($t) {
    
        if (!$t->hasPrev() ) { return false; }
        if (!$t->hasNext(3) ) { return false; }

        if ( ($t->checkClass(array('variable','property','property_static','tableau','method','method_static','functioncall')) ) && 
              $t->getNext()->checkCode('->') &&
              $t->getNext(1)->checkCode('{') &&
              $t->getNext(2)->checkNotClass('Token') &&
              $t->getNext(3)->checkCode('}') && 
              $t->getNext(4)->checkNotCode('(') 
            ) {

            $this->args   = array(0, 3);
            $this->remove = array(1, 2, 3, 4);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>