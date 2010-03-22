<?php

class function_reference_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
        
        $this->options = array(T_ABSTRACT, T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_FINAL);
    }

    function getTokens() {
        return array(T_FUNCTION);
    }
    
    function check($t) {
        if (!$t->hasNext(3)) { return false; }

        if ($t->checkNotToken(array(T_FUNCTION))) { return false; }
        if ($t->getNext()->checkNotCode('&')) { return false; }
        if ($t->getNext(1)->checkNotToken(T_STRING) && 
            $t->getNext(1)->checkNotClass('literals')) { return false; }
        if ($t->getNext(2)->checkNotClass('arglist')) { return false; }
//        if ($t->getNext(3)->checkNotClass('block') ) { return false; }

        mon_log(get_class($t->getNext(1))." => literals  (".__CLASS__.")");
        $regex = new modele_regex('literals',array(0), array());
        Token::applyRegex($t->getNext(1), 'literals', $regex);

        $this->args = array(1,2,3);
        $this->remove = array(1,2,3);

        if ($t->getNext(3)->checkClass('block') ) { 
            $this->args[] = 4;
            $this->remove[] = 4;
        } elseif ($t->getNext(3)->checkOperateur(';') ) { 
            $this->remove[] = 4;
        } elseif ($t->getNext(3)->checkClass('Token') ) { 
            return false;
        } else {
            die("Situation inconnue dans ".__METHOD__);
        }

        if ($t->hasPrev() && $t->getPrev()->checkToken($this->options)) {
            $this->args[] = -1;
            $this->remove[] = -1;
        }

        if ($t->hasPrev(1) && $t->getPrev(1)->checkToken($this->options)) {
            $this->args[] = -2;
            $this->remove[] = -2;
        }

        if ($t->hasPrev(2) && $t->getPrev(2)->checkToken($this->options)) {
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