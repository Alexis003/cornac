<?php

class property_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_VARIABLE,0);
    }
    
    function check($t) {
    
        if (!$t->hasPrev() ) { return false; }
        if (!$t->hasNext(2) ) { return false; }

        if ($t->getPrev()->checkCode('->') ) { return false; }

        if ( ($t->checkToken(T_VARIABLE) || 
              $t->checkClass(array('variable','property','tableau','method_static','method','functioncall','property_static','opappend')) ) && 
              $t->getNext()->checkCode('->') &&
              ($t->getNext(1)->checkToken(T_STRING) ||
               $t->getNext(1)->checkClass(array('variable','tableau'))) && 
              ($t->getNext(2)->checkNotCode(array('(')) ||
               $t->getNext(2)->checkClass(array('literals')))
              
            ) {

            if ($t->getNext(1)->checkClass("Token")) {
                $regex = new modele_regex('literals',array(0), array());
                Token::applyRegex($t->getNext(1), 'literals', $regex);
            }
            
            $this->args   = array(0, 2);
            $this->remove = array(1,2);

            mon_log(get_class($t)." => ".__CLASS__);
            return true; 
        } 
        return false;
    }
}
?>