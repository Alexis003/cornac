<?php

class function_abstract_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_FUNCTION);
    }    
    function check($t) {
        if (!$t->hasNext(2)) { return false; }
        
        if ($t->checkNotToken(array(T_FUNCTION))) { return false; }
        if ($t->getNext()->checkNotToken(T_STRING)) { return false; }
        if ($t->getNext(1)->checkNotClass('arglist')) { return false; }
        if ($t->getNext(2)->checkNotCode(';') ) { return false; }
        // @note : si ca compile et qu'on arrive ici, il y aura surement un abstract

        mon_log(get_class($t->getNext())." => literals  (".__CLASS__.")");
        $regex = new modele_regex('literals',array(0), array());
        Token::applyRegex($t->getNext(), 'literals', $regex);
        // #applyRegex

        $this->args = array(1,2,3);
        $this->remove = array(1,2,3);
        
        if ($t->hasPrev() && $t->getPrev()->checkToken(array(T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_FINAL, T_ABSTRACT))) {
            $this->args[] = -1;
            $this->remove[] = -1;
        }

        if ($t->hasPrev(1) && $t->getPrev(1)->checkToken(array(T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_FINAL, T_ABSTRACT))) {
            $this->args[] = -2;
            $this->remove[] = -2;
        }

        if ($t->hasPrev(2) && $t->getPrev(2)->checkToken(array(T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_FINAL, T_ABSTRACT))) {
            $this->args[] = -3;
            $this->remove[] = -3;
        }

        sort($this->args);
        sort($this->remove);

        mon_log(get_class($t)." => ".__CLASS__);
        return true;
    }
}

?>