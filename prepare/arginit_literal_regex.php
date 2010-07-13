<?php

class arginit_literal_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(Token::ANY_TOKEN);
    }

    function check($t) {
        if (!$t->hasNext(3) ) { return false; }
        if (!$t->hasPrev() ) { return false; }
        
        if ($t->getPrev()->checkNotCode(array('(',',')) &&
            $t->getPrev()->checkNotToken(array(T_VAR, T_PROTECTED, T_PRIVATE, T_PUBLIC))) { return false; }
        
        if ($t->checkNotClass(array('variable','constante','reference'))) { return false; }
        if ($t->getNext()->checkNotCode('=')) { return false; }
        if ($t->getNext(1)->checkNotClass(array('constante','literals','signe'))) { return false; }
        if ($t->getPrev(1)->checkToken(array(T_FOR,T_IF, T_ELSEIF))) { return false; }
        if ($t->getNext(2)->checkNotCode(array(',',')')))  { return false;}

        $this->args = array(0, 2);
        $this->remove = array(1, 2);
        
        mon_log(get_class($t)." => ".__CLASS__);
        return true; 
    }
}
?>