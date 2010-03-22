<?php

class ifthenelse_simples_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_ELSE);
    }
    
    function check($t) {
        if (!$t->hasNext() ) { return false; }

        if ($t->checkNotToken(T_ELSE)) { return false;}

        if ($t->getNext()->checkForBlock(true) && 
            (!$t->hasNext(1) || 
              ($t->getNext(1)->checkCode(';') ||
               $t->getNext(1)->checkToken(T_CLOSE_TAG)))
            ) {

            $regex = new modele_regex('block',array(0), array(1));
            Token::applyRegex($t->getNext(), 'block', $regex);

            mon_log(get_class($t)." => block (from ".get_class($t).") (".__CLASS__.")");
            return false; 
        } 

        if ( $t->getNext()->checkSubClass('instruction') &&
             ($t->getNext()->checkForBlock(true) ||
              $t->getNext()->checkClass(array('constante','signe','not','noscream','invert')) ||
              $t->getNext()->checkForVariable()
              ) &&
             ($t->getNext(1)->checkNotCode(array('=')) || 
              $t->getNext(1)->checkNotClass('Token'))
            ) {

            $regex = new modele_regex('block',array(0), array());
            Token::applyRegex($t->getNext(), 'block', $regex);

            mon_log(get_class($t)." => block (from instruction) (".__CLASS__.")");
            return false; 
        } 

        return false;
    }
}
?>