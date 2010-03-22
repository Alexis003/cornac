<?php

class method_accolade_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(0);
    }
    
    function check($t) {
    
        if (!$t->hasPrev() ) { return false; }
        if (!$t->hasNext(3) ) { return false; }

        if ( ($t->checkClass(array('variable','property','tableau','method','method_static','functioncall')) ) && 
              $t->getNext()->checkCode('->') &&
              $t->getNext(1)->checkCode('{') &&
              $t->getNext(2)->checkNotClass('Token') &&
              $t->getNext(3)->checkCode('}')) {
              
              if ( $t->getNext(4)->checkCode('(') &&
                   $t->getNext(5)->checkCode(')')) {
        
                   $regex = new modele_regex('functioncall',array(0), array(-1, 1, 2));
                   Token::applyRegex($t->getNext(2), 'functioncall', $regex);

                    mon_log(get_class($t)." => functioncall (".__CLASS__.")");
                    return false; 
              }

              if ( $t->getNext(4)->checkClass('arglist')) {
                   $regex = new modele_regex('functioncall',array(0, 2), array(-1, 1, 2));
                   Token::applyRegex($t->getNext(2), 'functioncall', $regex);

                    mon_log(get_class($t)." => functioncall (".__CLASS__.")");
                    return false; 
              }

              return false;
        } 
        return false;
    }
}
?>