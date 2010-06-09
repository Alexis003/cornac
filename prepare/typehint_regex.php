<?php

class typehint_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(0, T_ARRAY, T_STRING);
    }

    function check($t) {
        if (!$t->hasNext(1) ) { return false; }
        if (!$t->hasPrev() ) { return false; }

        if ($t->getPrev()->checkNotCode(array('(',','))) { return false; }
        if ($t->getPrev()->checkClass(array('arglist'))) { return false; }
        if ($t->getPrev(1)->checkToken(array(T_CATCH))) { return false; }
        if ($t->checkNotClass('Token')  &&  $t->checkToken(T_ARRAY)) { return false; }
        if ($t->checkToken(T_AS)) { return false; }
        // cas des " d'interpolation : " est alors un token seul
        if ($t->checkOperateur(array('"'))) { return false; } 

        if ($t->checkClass(array('variable'))) { return false; } 

        if ($t->getNext()->checkOperateur(array('&','|','^')) &&
            $t->getNext(1)->checkClass('variable')) {
            
            if ($t->checkClass('constante')) {
                return false;
            }
            
            $regex = new modele_regex('reference',array(1), array(1));
            Token::applyRegex($t->getNext(), 'reference', $regex);

            mon_log(get_class($t->getNext())." => reference (".__CLASS__.")");
            return false;
        }
        
        if ($t->getNext()->checkNotClass(array('variable','affectation','reference'))) { return false; }
        if ($t->getNext(1)->checkCode(array('='))) { return false; }
        if ($t->getNext(1)->checkNotCode(array(',',')'))) { return false; }
        
        $this->args = array(0,1);
        $this->remove = array(1);
        mon_log(get_class($t)." => ".__CLASS__."");
        
        return true;
    }
}
?>